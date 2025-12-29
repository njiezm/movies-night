<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
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
            'firstname' => 'required',
            'lastname' => 'required',
            'telephone' => 'required|unique:participants',
            'email' => 'nullable|email',
            'zipcode' => 'nullable',
        ]);

        // Traitement des options de contact
        $optin = $request->has('optin') ? $request->optin : 0;
        $bysms = false;
        $byemail = false;
        
        if ($optin == 1 && $request->has('contact_method')) {
            $contactMethod = $request->contact_method;
            $bysms = ($contactMethod == 1 || $contactMethod == 3);
            $byemail = ($contactMethod == 2 || $contactMethod == 3);
        }

        // Détermination de la source
        // Priorité : scan QR > source du formulaire > web par défaut
        if ($request->has('from_qr_scan') && $request->from_qr_scan == 1) {
            $source = 'salle';
        } elseif ($request->has('source') && !empty($request->source)) {
            $source = $request->source;
        } else {
            $source = 'web';
        }

        // Générer un slug unique pour le participant
        $slug = Str::slug($request->firstname . '-' . $request->lastname . '-' . $request->telephone);
        $originalSlug = $slug;
        $count = 1;
        
        while (Participant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $participant = Participant::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'zipcode' => $request->zipcode,
            'slug' => $slug,
            'optin' => $optin,
            'bysms' => $bysms,
            'byemail' => $byemail,
            'source' => $source,
        ]);

        // Si l'inscription vient d'un scan QR code, rediriger vers la page des films
        if ($request->has('from_qr_scan') && $request->has('film_slug')) {
            return redirect()->route('mes.films', ['participant' => $participant->slug, 'film_slug' => $request->film_slug])
                ->with('success', 'Inscription réussie !');
        }

        // Sinon, rediriger vers la page de rendez-vous
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