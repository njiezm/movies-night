<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
use App\Models\Base\Genesys;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function accueil($source = null)
    {
        return view('public.inscription', compact('source'));
    }

    public function showInscription($source = null)
    {
        return view('public.inscription', compact('source'));
    }

    public function storeInscription(Request $request)
{
    $request->validate([
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'telephone' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'zipcode' => 'nullable|string|max:10',
    ]);

    // 🔐 Chiffrement des données sensibles
    $firstname = Genesys::Crypt(ucfirst(strtolower($request->firstname)));
    $lastname  = Genesys::Crypt(ucfirst(strtolower($request->lastname)));
    $telephone = Genesys::Crypt($request->telephone);
    $email     = $request->email ? Genesys::Crypt(strtolower($request->email)) : null;

    // 🔍 Vérification unicité téléphone (APRÈS chiffrement)
    if (Participant::where('telephone', $telephone)->exists()) {
        return back()
            ->withErrors(['telephone' => 'Ce numéro est déjà inscrit'])
            ->withInput();
    }

    // 🔍 Vérification unicité email (si fourni)
    if ($email && Participant::where('email', $email)->exists()) {
        return back()
            ->withErrors(['email' => 'Cet email est déjà inscrit'])
            ->withInput();
    }

    // 📬 Gestion optin
    $optin = (int) $request->input('optin', 0);
    $bysms = false;
    $byemail = false;

    if ($optin === 1 && $request->has('contact_method')) {
        $contactMethod = $request->contact_method;
        $bysms = in_array($contactMethod, [1, 3]);
        $byemail = in_array($contactMethod, [2, 3]);
    }

    // 📍 Source
    if ($request->boolean('from_qr_scan')) {
        $source = 'salle';
    } elseif (!empty($request->source)) {
        $source = $request->source;
    } else {
        $source = 'web';
    }

    // 🔑 Slug sécurisé
    $slug = Genesys::GenCodeAlphaNum(20);

    // 💾 Création du participant
    $participant = Participant::create([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'telephone' => $telephone,
        'email' => $email,
        'zipcode' => $request->zipcode,
        'slug' => $slug,
        'optin' => $optin,
        'bysms' => $bysms,
        'byemail' => $byemail,
        'source' => $source,
    ]);

    // 🔁 Redirections
    if ($request->boolean('from_qr_scan') && $request->filled('film_slug')) {
        return redirect()
            ->route('mes.films', [
                'participant' => $participant->slug,
                'film_slug' => $request->film_slug
            ])
            ->with('success', 'Inscription réussie !');
    }

    return redirect()->route('rendez.vous');
}


    /**
 * Affiche la page de connexion express.
 */
public function showConnexionExpress()
{
    $film = null;
    // Si un film_slug est dans l'URL, on récupère le film correspondant
    if (request('film_slug')) {
        $film = Film::where('slug', request('film_slug'))->first();
    }

    // On passe le film (qui peut être null) à la vue
    return view('public.connexion-express', compact('film'));
}


    public function connexionExpress(Request $request)
    {
        $request->validate([
            'telephone' => 'required',
        ]);

        $participant = Participant::where('telephone', $request->telephone)->first();

        if (!$participant) {
            // Si film_slug est défini, rediriger vers la page d'inscription avec ce paramètre
            if ($request->film_slug) {
                return redirect()->route('inscription', ['source' => 'qr_scan'])
                    ->with('error', 'Numéro de téléphone non trouvé. Veuillez vous inscrire.')
                    ->with('film_slug', $request->film_slug);
            }
            
            // Sinon, rediriger vers la page d'inscription normale
            return redirect()->route('inscription')
                ->with('error', 'Numéro de téléphone non trouvé. Veuillez vous inscrire.');
        }

        // Si film_slug est défini, rediriger vers la page des films
        if ($request->film_slug) {
            return redirect()->route('mes.films', ['participant' => $participant->slug, 'film_slug' => $request->film_slug]);
        }

        // Sinon, rediriger vers la page des films sans film_slug
        return redirect()->route('mes.films', ['participant' => $participant->slug]);
    }

    public function scanQr($slug)
    {
        $film = Film::where('slug', $slug)->firstOrFail();
        return view('public.scan', compact('film'));
    }

    public function mesFilms($participant)
    {
        $participant = Participant::where('slug', $participant)->firstOrFail();
        $filmsVus = $participant->films;
        $total = Film::count();
        
        // Marquer le film comme vu si film_slug est fourni
        if (request()->has('film_slug')) {
            $film = Film::where('slug', request()->film_slug)->first();
            if ($film && !$filmsVus->contains($film->id)) {
                $participant->films()->attach($film->id);
                $filmsVus = $participant->fresh()->films;
            }
        }
        
        return view('public.mes-films', compact('participant', 'filmsVus', 'total'));
    }

    /**
     * Affiche la page du rendez-vous avec la liste des films.
     */
    public function rendezVous()
    {
        // On récupère tous les films, ordonnés par titre par exemple
        $films = Film::orderBy('title', 'asc')->get();

        // On passe les films à la vue
        return view('public.rendez-vous', compact('films'));
    }
}