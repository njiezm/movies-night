<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
use App\Models\Dotation;
use App\Models\Tirage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Cookie;


class AdminController extends Controller
{
    // Codes d'accès prédéfinis
    private $fullAccessCode = '123456';  // Accès complet avec dotations
    private $limitedAccessCode = '654321'; // Accès limité sans dotations
    
    public function loginold(Request $request)
    {
        $request->validate([
            'access_code' => 'required|digits:6'
        ]);
        
        $accessCode = $request->input('access_code');
        
        if ($accessCode === $this->fullAccessCode) {
            session(['admin_authenticated' => true, 'show_dotations' => true]);
            return redirect()->route('admin.stats');
        } elseif ($accessCode === $this->limitedAccessCode) {
            session(['admin_authenticated' => true, 'show_dotations' => false]);
            return redirect()->route('admin.stats');
        } else {
            return redirect()->back()->with('error', 'Code d\'accès incorrect');
        }
    }

    public function login(Request $request)
{
    $request->validate([
        'access_code' => 'required|digits:6'
    ]);

    $accessCode = $request->input('access_code');
    $remember = $request->has('remember'); 

    if ($accessCode === $this->fullAccessCode) {

        session([
            'admin_authenticated' => true,
            'show_dotations' => true
        ]);

        
        if ($remember) {
            Cookie::queue('admin_remember', 'full', 60 * 24 * 7); // 7 jours
        }

        return redirect()->route('admin.stats');

    } elseif ($accessCode === $this->limitedAccessCode) {

        session([
            'admin_authenticated' => true,
            'show_dotations' => false
        ]);

        if ($remember) {
            Cookie::queue('admin_remember', 'limited', 60 * 24 * 7);
        }

        return redirect()->route('admin.stats');

    } else {
        return back()->with('error', 'Code d\'accès incorrect');
    }
}

    
    public function logout(Request $request)
    {
        session()->forget(['admin_authenticated', 'show_dotations']);
        return redirect()->route('admin.login');
    }
    
    public function films()
    {
        $films = Film::withCount('participants')->get();
        return view('admin.films.index', compact('films'));
    }

    public function createFilm()
    {
        return view('admin.films.create');
    }

    private function generateUniqueSlug(int $length = 10): string
    {
        do {
            $slug = strtoupper(Str::random($length));
        } while (Film::where('slug', $slug)->exists());

        return $slug;
    }

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

public function updateFilm(Request $request, Film $film)
{
    $request->validate([
        'title'=>'required',
        'description'=>'nullable',
        'vignette'=>'nullable|image',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date'
    ]);

    // Conserver le slug existant pour maintenir le lien /scan/slug
    $film->update([
        'title'=>$request->title,
        'description'=>$request->description,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date
    ]);

    if($request->hasFile('vignette')){
        $film->vignette = $request->file('vignette')->store('vignettes','public');
        $film->save();
    }

    return redirect()->route('admin.films')->with('success','Film mis à jour !');
}

    public function editFilm(Film $film)
    {
        return view('admin.films.edit', compact('film'));
    }

  

    public function deleteFilm(Film $film)
    {
        $film->delete();
        return redirect()->route('admin.films')->with('success','Film supprimé !');
    }

    // --- DOTATIONS ---
    public function dotations()
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        $dotations = Dotation::all();
        return view('admin.dotations.index', compact('dotations'));
    }

    public function createDotation()
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        return view('admin.dotations.create');
    }

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

    public function drawTirage(Request $request, Tirage $tirage)
    {
        // Récupérer tous les participants
        $participants = Participant::all();
        
        if ($participants->isEmpty()) {
            return response()->json(['error' => 'Aucun participant disponible pour le tirage au sort.'], 400);
        }
        
        // Vérifier s'il reste des dotations disponibles
        $dotation = $tirage->dotation;
        if ($dotation->remaining_count <= 0) {
            return response()->json(['error' => 'Il ne reste plus de dotations disponibles pour ce tirage.'], 400);
        }
        
        // Choisir un gagnant au hasard
        $winner = $participants->random();
        
        // Enregistrer le gagnant
        $tirage->winner_id = $winner->id;
        $tirage->save();
        
        // Retourner les informations du gagnant au format JSON
        return response()->json([
            'success' => true,
            'message' => 'Le gagnant du tirage au sort est ' . $winner->firstname . ' ' . $winner->lastname . ' !',
            'winner_firstname' => $winner->firstname,
            'winner_lastname' => $winner->lastname,
            'winner_email' => $winner->email ?? 'Non spécifié'
        ]);
    }
    public function editDotation(Dotation $dotation)
    {
        // Vérifier si l'utilisateur a accès à cette section
        if (!session('show_dotations')) {
            return redirect()->route('admin.stats')->with('error', 'Vous n\'avez pas accès à cette section');
        }
        
        return view('admin.dotations.edit', compact('dotation'));
    }



   // --- STATISTIQUES ---
    public function stats()
    {
        $totalParticipants = Participant::count();
        $totalOptinParticipants = Participant::where('optin', 1)->count();
        $totalFilms = Film::count();
        $films = Film::withCount('participants')->get();
        
        // Récupère les 4 meilleurs participants
        $ranking = Participant::withCount('films')
                            ->orderByDesc('films_count')
                            ->take(4)
                            ->get();

        return view('admin.stats', compact('totalParticipants','totalOptinParticipants','totalFilms','films','ranking'));
    }

    // --- TIRAGES AU SORT ---
    public function tirages()
    {
        $tirages = Tirage::with('dotation')->get();
        $dotations = Dotation::all();
        return view('admin.tirages.index', compact('tirages', 'dotations'));
    }

    public function createTirage()
    {
        $dotations = Dotation::all();
        return view('admin.tirages.create', compact('dotations'));
    }

    public function storeTirage(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'dotation_id' => 'required|exists:dotations,id',
            'date' => 'required|date',
        ]);

        Tirage::create($request->all());
        return redirect()->route('admin.tirages')->with('success', 'Tirage au sort ajouté !');
    }

    public function editTirage(Tirage $tirage)
    {
        $dotations = Dotation::all();
        return view('admin.tirages.edit', compact('tirage', 'dotations'));
    }

    public function updateTirage(Request $request, Tirage $tirage)
    {
        $request->validate([
            'title' => 'required',
            'dotation_id' => 'required|exists:dotations,id',
            'date' => 'required|date',
        ]);

        $tirage->update($request->all());
        return redirect()->route('admin.tirages')->with('success', 'Tirage au sort mis à jour !');
    }

    public function deleteTirage(Tirage $tirage)
    {
        $tirage->delete();
        return redirect()->route('admin.tirages')->with('success', 'Tirage au sort supprimé !');
    }

    public function drawTirageold(Request $request, Tirage $tirage)
    {
        // Récupérer tous les participants
        $participants = Participant::all();
        
        if ($participants->isEmpty()) {
            return redirect()->route('admin.tirages')->with('error', 'Aucun participant disponible pour le tirage au sort.');
        }
        
        // Choisir un gagnant au hasard
        $winner = $participants->random();
        
        // Enregistrer le gagnant
        $tirage->winner_id = $winner->id;
        $tirage->save();
        
        return redirect()->route('admin.tirages')->with('success', 'Le gagnant du tirage au sort est ' . $winner->firstname . ' ' . $winner->lastname . ' !');
    }

    public function getFilmData(Film $film)
{
    // Formater les dates 
    $film->start_date = $film->start_date ? date('Y-m-d', strtotime($film->start_date)) : null;
    $film->end_date = $film->end_date ? date('Y-m-d', strtotime($film->end_date)) : null;
    
    return response()->json($film);
}

    public function getTirageData(Tirage $tirage)
{
    // Charger la relation avec le gagnant
    $tirage->load('winner');
    
    return response()->json($tirage);
}
}