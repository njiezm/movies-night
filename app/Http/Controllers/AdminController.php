<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
use App\Models\Dotation;
use App\Models\Tirage;
use App\Models\Setting;
use App\Models\Base\Genesys;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;  
use Carbon\Carbon;

class AdminController extends Controller
{
    // ==================== AUTHENTIFICATION ====================
    
    /**
     * Gère la connexion à l'administration
     */
    public function login(Request $request)
    {
        $request->validate([
            'access_code' => 'required|digits:6'
        ]);

        $accessCode = $request->input('access_code');
        $remember = $request->has('remember'); 
        
        // Récupérer les codes depuis la base de données
        $fullAccessCode = Setting::get('full_access_code', '123456');
        $limitedAccessCode = Setting::get('limited_access_code', '654321');

        if ($accessCode === $fullAccessCode) {
            session([
                'admin_authenticated' => true,
                'show_dotations' => true
            ]);
            
            if ($remember) {
                Cookie::queue('admin_remember', 'full', 60 * 24 * 7); // 7 jours
            }

            return redirect()->route('admin.stats');
        } 
        elseif ($accessCode === $limitedAccessCode) {
            session([
                'admin_authenticated' => true,
                'show_dotations' => false
            ]);

            if ($remember) {
                Cookie::queue('admin_remember', 'limited', 60 * 24 * 7);
            }

            return redirect()->route('admin.stats');
        } 
        else {
            return back()->with('error', 'Code d\'accès incorrect');
        }
    }
    
    /**
     * Déconnexion de l'administration
     */
    public function logout(Request $request)
    {
        session()->forget(['admin_authenticated', 'show_dotations']);
        return redirect()->route('admin.login');
    }
    
    // ==================== RÉGLAGES ====================
    
    /**
     * Affiche la page des réglages
     */
    public function settings()
    {
        // Vérifier si l'utilisateur a un accès complet
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        // Récupérer tous les réglages
        $settings = [
            'full_access_code' => Setting::get('full_access_code', '123456'),
            'limited_access_code' => Setting::get('limited_access_code', '654321'),
            'opening_date' => Setting::get('opening_date', now()->format('Y-m-d')),
            'closing_date' => Setting::get('closing_date', now()->addMonths(6)->format('Y-m-d')),
            'min_age' => Setting::get('min_age', '16'),
        ];
        
        return view('admin.settings.index', compact('settings'));
    }
    
    /**
     * Met à jour les réglages
     */
    public function updateSettings(Request $request)
    {
        // Vérifier si l'utilisateur a un accès complet
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        $request->validate([
            'full_access_code' => 'required|digits:6',
            'limited_access_code' => 'required|digits:6|different:full_access_code',
            'opening_date' => 'required|date',
            'closing_date' => 'required|date|after:opening_date',
            'min_age' => 'required|integer|min:0|max:18',
        ]);
        
        // Mettre à jour les réglages
        Setting::set('full_access_code', $request->full_access_code);
        Setting::set('limited_access_code', $request->limited_access_code);
        Setting::set('opening_date', $request->opening_date);
        Setting::set('closing_date', $request->closing_date);
        Setting::set('min_age', $request->min_age);
        
        return redirect()->route('admin.settings')->with('success', 'Réglages mis à jour avec succès !');
    }
    
    // ==================== FILMS ====================
    
    /**
     * Affiche la liste des films
     */
    public function films()
    {
        $films = Film::withCount('participants')->get();
        return view('admin.films.index', compact('films'));
    }

    /**
     * Affiche le formulaire de création de film
     */
    public function createFilm()
    {
        return view('admin.films.create');
    }

    /**
     * Enregistre un nouveau film
     */
    public function storeFilm(Request $request)
{
    $request->validate([
        'title' => 'required',
        'description' => 'nullable',
        'vignette' => 'nullable|image',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date'
    ]);

    // Slug unique
    $slug = $this->generateUniqueSlug();

    // Upload vignette
    $vignettePath = null;
    if ($request->hasFile('vignette')) {
        $vignettePath = $request->file('vignette')->store('vignettes', 'public');
    }

    $film = Film::create([
        'title' => $request->title,
        'description' => $request->description,
        'slug' => $slug,
        'vignette' => $vignettePath,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date
    ]);


    // Créer un tirage mensuel automatiquement pour ce film
    $tirageDate = $film->end_date ?? now()->addMonth();

    
    
    // Vérifier d'abord qu'il existe des dotations mensuelles
    $monthlyDotationExists = Dotation::where('is_big_tas', false)->exists();
    
    if (!$monthlyDotationExists) {
        \Log::warning('Aucune dotation mensuelle disponible pour créer un tirage pour le film: ' . $film->title);
        // Vous pouvez aussi retourner un message d'erreur si nécessaire
    } else {
        $tirage = $this->createMonthlyDraw($tirageDate);
        
        if ($tirage) {
            // Associer directement le tirage au film lors de la création
            $tirage->film_id = $film->id;
            $tirage->save();
            
            \Log::info('Tirage mensuel créé avec succès pour le film: ' . $film->title . ' (ID: ' . $tirage->id . ')');
        } else {
            \Log::error('Échec de la création du tirage mensuel pour le film: ' . $film->title);
        }
    }

    // Créer dossier qrcodes si absent
    if (!file_exists(public_path('qrcodes'))) {
        mkdir(public_path('qrcodes'), 0755, true);
    }

    // Génération QR code avec endroid/qr-code
    $qrcodePath = "qrcodes/film-{$film->id}.png";
    $qrLink = route('scan', $film->slug);
    
    try {
        $qrCode = QrCode::create($qrLink)
            ->setSize(300)
            ->setMargin(10);
            
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        $result->saveToFile(public_path($qrcodePath));
        
        $film->update(['qrcode' => $qrcodePath]);
    } catch (\Exception $e) {
        \Log::error('Erreur lors de la génération du QR code: ' . $e->getMessage());
    }

    return redirect()->route('admin.films')->with('success', 'Film ajouté avec succès !');
}

    /**
     * Affiche le formulaire d'édition de film
     */
    public function editFilm(Film $film)
    {
        return view('admin.films.edit', compact('film'));
    }

    /**
     * Met à jour un film
     */
    public function updateFilm(Request $request, Film $film)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'vignette' => 'nullable|image',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        // Conserver le slug existant pour maintenir le lien /scan/slug
        $film->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        if ($request->hasFile('vignette')) {
            $film->vignette = $request->file('vignette')->store('vignettes', 'public');
            $film->save();
        }

        return redirect()->route('admin.films')->with('success', 'Film mis à jour !');
    }

    /**
     * Supprime un film et ses tirages associés (s'ils n'ont pas de gagnant)
     */
    public function deleteFilm(Film $film)
    {
        // Vérifier si un tirage associé à ce film a déjà un gagnant
        $tiragesWithWinners = $film->tirages()->whereNotNull('winner_id')->get();
        
        if ($tiragesWithWinners->isNotEmpty()) {
            // Récupérer les informations des gagnants pour le message d'erreur
            $winnersInfo = [];
            foreach ($tiragesWithWinners as $tirage) {
                if ($tirage->winner) {
                    $winnerName = Genesys::Decrypt($tirage->winner->firstname) . ' ' . Genesys::Decrypt($tirage->winner->lastname);
                    $winnersInfo[] = "Tirage '{$tirage->title}' - Gagnant: {$winnerName}";
                }
            }
            
            return redirect()->route('admin.films')->with('error', 
                'Impossible de supprimer ce film car un ou plusieurs tirages associés ont déjà des gagnants :<br>' . 
                implode('<br>', $winnersInfo)
            );
        }
        
        // Si aucun tirage n'a de gagnant, supprimer les tirages associés
        $film->tirages()->delete();
        
        // Puis supprimer le film
        $film->delete();
        
        return redirect()->route('admin.films')->with('success', 'Film et ses tirages associés ont été supprimés !');
    }

    /**
     * Retourne les données d'un film au format JSON
     */
    public function getFilmData(Film $film)
    {
        // Formater les dates 
        $film->start_date = $film->start_date ? date('Y-m-d', strtotime($film->start_date)) : null;
        $film->end_date = $film->end_date ? date('Y-m-d', strtotime($film->end_date)) : null;
        
        return response()->json($film);
    }
    
    // ==================== DOTATIONS ====================
    
    /**
     * Affiche la liste des dotations
     */
    public function dotations()
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        $dotations = Dotation::all();
        return view('admin.dotations.index', compact('dotations'));
    }

    /**
     * Affiche le formulaire de création de dotation
     */
    public function createDotation()
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        return view('admin.dotations.create');
    }

    /**
     * Enregistre une nouvelle dotation
     */
    public function storeDotation(Request $request)
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'dotationdate' => 'required|date|after_or_equal:today',
            'quantity' => 'required|integer|min:1'
        ], [
            'title.required' => 'Le titre est obligatoire',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères',
            'dotationdate.required' => 'La date est obligatoire',
            'dotationdate.after_or_equal' => 'La date doit être aujourd\'hui ou dans le futur',
            'quantity.required' => 'La quantité est obligatoire',
            'quantity.min' => 'La quantité doit être d\'au moins 1'
        ]);

        Dotation::create($request->all());
        return redirect()->route('admin.dotations')->with('success', 'Dotation ajoutée avec succès !');
    }

    /**
     * Affiche le formulaire d'édition de dotation
     */
    public function editDotation(Dotation $dotation)
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        return view('admin.dotations.edit', compact('dotation'));
    }

    /**
     * Met à jour une dotation
     */
    public function updateDotation(Request $request, Dotation $dotation)
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'dotationdate' => 'required|date|after_or_equal:today',
            'quantity' => 'required|integer|min:' . ($dotation->attributed_count ?? 1)
        ], [
            'title.required' => 'Le titre est obligatoire',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères',
            'dotationdate.required' => 'La date est obligatoire',
            'dotationdate.after_or_equal' => 'La date doit être aujourd\'hui ou dans le futur',
            'quantity.required' => 'La quantité est obligatoire',
            'quantity.min' => 'La quantité ne peut pas être inférieure au nombre de dotations déjà attribuées (' . ($dotation->attributed_count ?? 0) . ')'
        ]);

        // Mettez à jour chaque champ
        $dotation->title = $request->title;
        $dotation->dotationdate = $request->dotationdate;
        $dotation->quantity = $request->quantity;
        $dotation->save();
        
        return redirect()->route('admin.dotations')->with('success', 'Dotation mise à jour avec succès !');
    }

    /**
     * Supprime une dotation
     */
    public function deleteDotation(Dotation $dotation)
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        // Vérifier si des tirages sont associés à cette dotation
        if ($dotation->tirages()->count() > 0) {
            return redirect()->route('admin.dotations')->with('error', 'Impossible de supprimer cette dotation car elle est associée à des tirages. Supprimez d\'abord les tirages associés.');
        }
        
        $dotation->delete();
        return redirect()->route('admin.dotations')->with('success', 'Dotation supprimée !');
    }
    
    // ==================== TIRAGES ====================
    
    /**
     * Affiche la liste des tirages
     */
    public function tirages()
    {
        // Récupérer tous les tirages avec leurs relations
        $allTirages = Tirage::with(['film', 'dotation', 'winner'])->orderBy('date', 'asc')->get();
        
        // Séparer les tirages mensuels et le BIG TAS
        $monthlyTirages = $allTirages->where('is_big_tas', false);
        $bigTirages = $allTirages->where('is_big_tas', true);
        
        // Récupérer les dotations séparées par type
        $monthlyDotations = Dotation::where('is_big_tas', false)->get();
        $bigTasDotations = Dotation::where('is_big_tas', true)->get();
        
        $films = Film::all();
        
        // Vérifier si un BIG TAS existe déjà
        $bigTasExists = Tirage::where('is_big_tas', true)->exists();
        
        return view('admin.tirages.index', compact('monthlyTirages', 'bigTirages', 'monthlyDotations', 'bigTasDotations', 'films', 'bigTasExists'));
    }

    /**
     * Affiche le formulaire de création de tirage
     */
    public function createTirage()
    {
        $dotations = Dotation::all();
        return view('admin.tirages.create', compact('dotations'));
    }

    /**
     * Enregistre un nouveau tirage
     */
    public function storeTirage(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'is_big_tas' => 'required|boolean'
        ]);
        
        $date = $request->date;
        $isBigTas = $request->is_big_tas;
        
        if ($isBigTas) {
            // Vérifier si un BIG TAS existe déjà
            $existingBigTas = Tirage::where('is_big_tas', true)->first();
            if ($existingBigTas) {
                return redirect()->route('admin.tirages')->with('error', 'Un BIG TAS existe déjà !');
            }
            
            // Créer le tirage BIG TAS
            $tirage = $this->createBigTasDraw($date);
            
            if (!$tirage) {
                return redirect()->route('admin.tirages')->with('error', 'Aucune dotation BIG TAS disponible. Veuillez en créer une d\'abord.');
            }
        } else {
            // Créer un tirage mensuel
            $tirage = $this->createMonthlyDraw($date);
            
            if (!$tirage) {
                return redirect()->route('admin.tirages')->with('error', 'Aucune dotation mensuelle disponible. Veuillez en créer une d\'abord.');
            }
            
            // Associer un film si spécifié
            if ($request->has('film_id') && $request->film_id) {
                $tirage->update(['film_id' => $request->film_id]);
            }
        }
        
        return redirect()->route('admin.tirages')->with('success', 'Tirage au sort ajouté !');
    }

    /**
     * Met à jour un tirage
     */
    public function updateTirage(Request $request, Tirage $tirage)
{
    $request->validate([
        'date' => 'required|date',
    ]);
    
    // Mettre à jour la date
    $tirage->date = $request->date;
    
    // Mettre à jour le titre en fonction du type de tirage
    if ($tirage->is_big_tas) {
        $tirage->title = 'BIG TAS - Tirage pour les participants ayant vu tous les films';
    } else {
        $tirage->title = $this->formatMonthlyDrawTitle($request->date);
    }
    
    // Mettre à jour le film si spécifié
    if ($request->has('film_id')) {
        $tirage->film_id = $request->film_id;
    }
    
    // AJOUT : Mettre à jour la condition de récupération
    if ($request->has('condition_recuperation')) {
        $tirage->condition_recuperation = $request->condition_recuperation;
    }
    
    $tirage->save();
    
    return redirect()->route('admin.tirages')->with('success', 'Tirage au sort mis à jour !');
}

    /**
     * Supprime un tirage
     */
    public function deleteTirage(Tirage $tirage)
    {
        $tirage->delete();
        return redirect()->route('admin.tirages')->with('success', 'Tirage au sort supprimé !');
    }

    /**
     * Effectue un tirage au sort
     */
    public function drawTirage(Request $request, Tirage $tirage)
    {
        // Si c'est une demande de confirmation, on met à jour le champ conf
        if ($request->has('confirm') && $request->confirm) {
            $tirage->conf = true;
            $tirage->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Le tirage a été confirmé avec succès !'
            ]);
        }
        
        // Récupérer les participants éligibles pour ce tirage
        $participants = [];
        
        if ($tirage->is_big_tas) {
        $totalFilms = Film::count();

        $excludedWinnerIds = Tirage::where('is_big_tas', false)
            ->whereNotNull('winner_id')
            ->pluck('winner_id');

        $participants = Participant::where('age', 'plus_de_18')
            ->whereNotIn('id', $excludedWinnerIds)
            ->whereIn('id', function ($query) use ($totalFilms) {
                $query->select('participant_id')
                    ->from('participant_film')
                    ->groupBy('participant_id')
                    ->havingRaw('COUNT(*) = ?', [$totalFilms]);
            })
            ->get();

    }

        elseif ($tirage->film_id) {
            // Pour un tirage de film, on récupère les participants qui ont vu ce film plus de 18 et n'ayanbt jaamis gagné
           $excludedWinnerIds = Tirage::where('is_big_tas', false)
                ->whereNotNull('winner_id')
                ->pluck('winner_id');

            $participants = $tirage->film->participants()
                ->where('age', 'plus_de_18')
                ->whereNotIn('participants.id', $excludedWinnerIds)
                ->get();

        } else {
            // Pour un tirage classique, on récupère tous les participants
            $participants = Participant::where('age', 'plus_de_18')->get();
        }
        
        if ($participants->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'Aucun participant éligible disponible pour le tirage au sort.'
            ], 400);
        }
        
        // Vérifier s'il reste des dotations disponibles
        $dotation = $tirage->dotation;
        if ($dotation && $dotation->remaining_count <= 0) {
            return response()->json([
                'success' => false,
                'error' => 'Il ne reste plus de dotations disponibles pour ce tirage.'
            ], 400);
        }
        
        // Choisir un gagnant au hasard
        $winner = $participants->random();
        
        // Enregistrer le gagnant mais sans confirmer le tirage
        $tirage->winner_id = $winner->id;
        $tirage->conf = false; // Par défaut, le tirage n'est pas confirmé
        $tirage->save();
        
        // Déchiffrer les données du gagnant
        $winnerFirstname = Genesys::Decrypt($winner->firstname);
        $winnerLastname = Genesys::Decrypt($winner->lastname);
        $winnerEmail = $winner->email ? Genesys::Decrypt($winner->email) : 'Non spécifié';
        $winnerTelephone = Genesys::Decrypt($winner->telephone);
        
        // S'assurer que les données sont en UTF-8
        $winnerFirstname = mb_convert_encoding($winnerFirstname, 'UTF-8', 'UTF-8');
        $winnerLastname = mb_convert_encoding($winnerLastname, 'UTF-8', 'UTF-8');
        $winnerEmail = mb_convert_encoding($winnerEmail, 'UTF-8', 'UTF-8');
        $winnerTelephone = mb_convert_encoding($winnerTelephone, 'UTF-8', 'UTF-8');
        
        // Retourner les informations du gagnant au format JSON
        return response()->json([
            'success' => true,
            'message' => 'Le gagnant du tirage au sort est ' . $winnerFirstname . ' ' . $winnerLastname . ' !',
            'winner_firstname' => $winnerFirstname,
            'winner_lastname' => $winnerLastname,
            'winner_email' => $winnerEmail,
            'winner_telephone' => $winnerTelephone,
            'confirmed' => $tirage->conf
        ]);
    }

    /**
     * Crée un BIG TAS
     */
    public function createBigTas()
    {
        // Vérifier si un BIG TAS existe déjà
        $existingBigTas = Tirage::where('is_big_tas', true)->first();
        if ($existingBigTas) {
            return redirect()->route('admin.tirages')->with('error', 'Un BIG TAS existe déjà !');
        }
        
        // Créer le BIG TAS avec la fonction dédiée
        $tirage = $this->createBigTasDraw(now()->addMonth());
        
        if (!$tirage) {
            return redirect()->route('admin.tirages')->with('error', 'Aucune dotation BIG TAS disponible. Veuillez en créer une d\'abord.');
        }
        
        return redirect()->route('admin.tirages')->with('success', 'BIG TAS créé avec succès !');
    }

    /**
     * Retourne les données d'un tirage au format JSON
     */
    public function getTirageData(Tirage $tirage)
    {
        $tirage->load(['winner', 'dotation']);

        return response()->json([
            'id' => $tirage->id,
            'title' => $tirage->title,
            'date' => $tirage->date,
            'dotation_id' => $tirage->dotation_id,
            'winner' => $tirage->winner ? [
                'firstname' => $tirage->winner->firstname,
                'lastname' => $tirage->winner->lastname,
                'telephone' => $tirage->winner->telephone,
                'email' => $tirage->winner->email
                    ? $tirage->winner->email
                    : null,
            ] : null
        ]);
    }
    
    // ==================== STATISTIQUES ====================
    
    /**
     * Affiche les statistiques
     */
    public function stats()
    {
        $totalParticipants = Participant::count();
        $totalOptinParticipants = Participant::where('optin', 1)->count();
        $totalFilms = Film::count();
        $films = Film::withCount('participants')->get();
        
        // Calcul de l'éligibilité par film
        $filmsEligibility = [];
        foreach ($films as $film) {
            // Nombre de participants éligibles pour ce film (TAS mensuel)
            $eligibleCount = $film->participants()->count();
            
            $filmsEligibility[] = [
                'film' => $film,
                'eligible_count' => $eligibleCount
            ];
        }
        
        // Calcul du nombre de participants éligibles au BIG TAS (ceux qui ont vu tous les films)
        $bigTasEligible = 0;
        if ($totalFilms > 0) {
            // Récupérer tous les participants avec le nombre de films qu'ils ont vus
            $participantsWithFilmCount = Participant::withCount('films')->get();
            
            // Compter ceux qui ont vu tous les films
            $bigTasEligible = $participantsWithFilmCount->where('films_count', $totalFilms)->count();
        }
        
        // Récupère les 4 meilleurs participants 
        $ranking = Participant::withCount('films')
                            ->orderByDesc('films_count')
                            ->take(4)
                            ->get()
                            ->map(function($participant) {
                                $participant->firstname = $participant->firstname;
                                $participant->lastname = $participant->lastname;
                                return $participant;
                            });

        return view('admin.stats', compact(
            'totalParticipants',
            'totalOptinParticipants',
            'totalFilms',
            'films',
            'filmsEligibility',
            'bigTasEligible',
            'ranking'
        ));
    }
    
    /**
     * Exporte les participants
     */
    public function exportParticipants()
    {
        $participants = Participant::all();
        
        // Préparation des données avec déchiffrement
        $exportData = $participants->map(function($participant) {
            return [
                'id' => $participant->id,
                'firstname' => Genesys::Decrypt($participant->firstname),
                'lastname' => Genesys::Decrypt($participant->lastname),
                'telephone' => Genesys::Decrypt($participant->telephone),
                'email' => $participant->email ? Genesys::Decrypt($participant->email) : null,
                'zipcode' => $participant->zipcode,
                'optin' => $participant->optin,
                'bysms' => $participant->bysms,
                'byemail' => $participant->byemail,
                'source' => $participant->source,
                'created_at' => $participant->created_at,
            ];
        });
        
        // Ici vous pourriez retourner un CSV ou un autre format d'export
        return response()->json($exportData);
    }
    
    // ==================== FONCTIONS UTILITAIRES ====================
    
    /**
     * Génère un slug unique
     */
    private function generateUniqueSlug(int $length = 10): string
    {
        do {
            $slug = strtoupper(Str::random($length));
        } while (Film::where('slug', $slug)->exists());

        return $slug;
    }
    
    /**
     * Détermine le numéro du tirage mensuel en fonction de la période
     */
    private function getMonthlyDrawNumber($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        
        // Compter le nombre de tirages mensuels avant cette date
        $count = Tirage::where('is_big_tas', false)
            ->whereYear('date', $year)
            ->whereMonth('date', '<=', $month)
            ->count();
            
        return $count;
    }
    
    /**
     * Formate le titre du tirage mensuel avec son numéro
     */
    private function formatMonthlyDrawTitle($date)
    {
        $number = $this->getMonthlyDrawNumber($date);
        $monthName = Carbon::parse($date)->format('F');
        $year = Carbon::parse($date)->year;
        
        // Gérer la numérotation en français (1er, 2ème, 3ème, etc.)
        $number = $number + 1;
        if ($number == 1) {
            $numberText = '1er';
        } else {
            $numberText = $number . 'ème';
        }
        
        // Traduire le nom du mois en français
        $months = [
            'January' => 'janvier',
            'February' => 'février',
            'March' => 'mars',
            'April' => 'avril',
            'May' => 'mai',
            'June' => 'juin',
            'July' => 'juillet',
            'August' => 'août',
            'September' => 'septembre',
            'October' => 'octobre',
            'November' => 'novembre',
            'December' => 'décembre'
        ];
        
        $frenchMonth = $months[$monthName] ?? $monthName;
        
        return "{$numberText} tirage - {$frenchMonth} {$year}";
    }
    
    /**
     * Crée automatiquement un tirage mensuel avec une dotation mensuelle
     */
    private function createMonthlyDraw($date)
    {
        // Trouver une dotation mensuelle disponible (non BIG TAS)
        $dotation = Dotation::where('is_big_tas', false)
            ->where('quantity', '>', 0)
            ->first();
            
        if (!$dotation) {
            return null;
        }
        
        // Créer le tirage mensuel
        return Tirage::create([
            'title' => $this->formatMonthlyDrawTitle($date),
            'dotation_id' => $dotation->id,
            'date' => $date,
            'condition_recuperation' => 'Présenter le QR code à la caisse',
            'is_big_tas' => false
        ]);
    }
    
    /**
     * Crée automatiquement un tirage BIG TAS avec une dotation BIG TAS
     */
    private function createBigTasDraw($date)
    {
        // Trouver une dotation BIG TAS disponible
        $dotation = Dotation::where('is_big_tas', true)
            ->where('quantity', '>', 0)
            ->first();
            
        if (!$dotation) {
            return null;
        }
        
        // Créer le tirage BIG TAS
        return Tirage::create([
            'title' => 'BIG TAS - Tirage pour les participants ayant vu tous les films',
            'dotation_id' => $dotation->id,
            'date' => $date,
            'condition_recuperation' => 'Présenter tous les QR codes des films à la caisse',
            'is_big_tas' => true
        ]);
    }
}