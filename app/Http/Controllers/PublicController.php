<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
use App\Models\Setting;
use App\Models\Base\Genesys;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    /**
     * Affiche la page d'inscription en vérifiant l'état du marathon
     */
    public function showInscription($source = null)
    {
        if ($redirect = $this->checkMarathonStatus()) {
            return $redirect;
        }

        return view('public.inscription', compact('source'));
    }

    /**
     * Traite l'inscription d'un participant
     */
    public function storeInscription(Request $request)
    {
        $minAge = Setting::get('min_age', 14);

        // Validation
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'zipcode' => 'nullable|string|max:10',
            'age' => "required|integer|min:$minAge|max:120",
            'optin' => 'required|boolean',
        ]);

        $isOver14 = $request->input('age') >= $minAge;
        if (!$isOver14) {
            return back()
                ->with('error', "Vous devez avoir au moins $minAge ans pour participer.")
                ->withInput();
        }

        // Chiffrement
        $firstname = Genesys::Crypt(ucfirst(strtolower($request->firstname)));
        $lastname  = Genesys::Crypt(ucfirst(strtolower($request->lastname)));
        $telephone = Genesys::Crypt($request->telephone);
        $email     = $request->email ? Genesys::Crypt(strtolower($request->email)) : null;

        // Unicité
        if (Participant::where('telephone', $telephone)->exists()) {
            return back()->withErrors(['telephone' => 'Ce numéro est déjà inscrit'])->withInput();
        }
        if ($email && Participant::where('email', $email)->exists()) {
            return back()->withErrors(['email' => 'Cet email est déjà inscrit'])->withInput();
        }

        // Gestion optin
        $optin = (int) $request->input('optin', 0);
        $bysms = false;
        $byemail = false;
        if ($optin === 1 && $request->has('contact_method')) {
            $contactMethod = $request->contact_method;
            $bysms = in_array($contactMethod, [1, 3]);
            $byemail = in_array($contactMethod, [2, 3]);
        }

        // Source
        $source = $request->boolean('from_qr_scan') ? 'salle' : ($request->source ?? 'web');

        // Slug sécurisé
        $slug = Genesys::GenCodeAlphaNum(20);

        // Création
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
            'is_over_14' => $isOver14,
        ]);

        // Redirection après inscription
        if ($request->boolean('from_qr_scan') && $request->filled('film_slug')) {
            return redirect()->route('mes.films', [
                'participant' => $participant->slug,
                'film_slug' => $request->film_slug
            ])->with('success', 'Inscription réussie !');
        }

        return redirect()->route('rendez.vous')->with('success', 'Inscription réussie !');
    }

    /**
     * Affiche la page du rendez-vous
     */
    public function rendezVous()
    {
        if ($redirect = $this->checkMarathonStatus()) {
            return $redirect;
        }

        $films = Film::orderBy('title', 'asc')->get();
        return view('public.rendez-vous', compact('films'));
    }

    /**
     * Affiche les films d'un participant
     */
   public function mesFilms($participantSlug)
    {
        if ($redirect = $this->checkMarathonStatus()) {
            return $redirect;
        }

        // Récupération du participant
        $participant = Participant::where('slug', $participantSlug)->firstOrFail();
        $filmsVus = $participant->films;
        $total = Film::count();

        // Film scanné (optionnel)
        $film = null;
        $filmSlug = request()->input('film_slug');
        if ($filmSlug) {
            $film = Film::where('slug', $filmSlug)->first();

           
        if ($film) {
                // Si le participant a déjà scanné ce film → redirection vers dejaJoue
                if ($filmsVus->contains($film->id)) {
                    return redirect()->route('deja.joue', ['participant' => $participant->slug]);
                }

                // Sinon → marquer le film comme vu
                $participant->films()->attach($film->id);
                // Récupérer la liste mise à jour
                $filmsVus = $participant->fresh()->films;
            }
        }

        // Passer $film à la vue seulement s'il existe
        return view('public.mes-films', compact('participant', 'filmsVus', 'total', 'film'));
    }


    /**
     * Connexion express par téléphone
     */
    public function connexionExpress(Request $request)
    {
        $request->validate(['telephone' => 'required']);
        $encryptedPhone = Genesys::Crypt($request->telephone);

        $participant = Participant::where('telephone', $encryptedPhone)->first();

        if (!$participant) {
            $route = $request->film_slug ? route('inscription', ['source' => 'qr_scan']) : route('inscription');
            return redirect($route)->with('error', 'Numéro de téléphone non trouvé. Veuillez vous inscrire.')
                                   ->with('film_slug', $request->film_slug);
        }

        $params = ['participant' => $participant->slug];
        if ($request->film_slug) $params['film_slug'] = $request->film_slug;

        return redirect()->route('mes.films', $params);
    }

    /**
     * Affiche la page de scan QR code
     */
    public function scanQr($slug)
    {
        if ($redirect = $this->checkMarathonStatus()) {
            return $redirect->with('error', 'Le marathon n\'est pas ouvert.');
        }

        $film = Film::where('slug', $slug)->firstOrFail();
        return view('public.scan-connexion', compact('film'));
    }

    /**
     * Page de patience
     */
    public function patience()
    {
        $openingDate = Setting::get('opening_date');
        $message = request('message', 'Le marathon n\'a pas encore commencé. Revenez bientôt !');
        return view('public.patience', compact('openingDate', 'message'));
    }

    /**
     * Page terminé
     */
    public function termine()
    {
        $closingDate = Setting::get('closing_date');
        $message = request('message', 'Le marathon est terminé. Merci d\'avoir participé !');
        return view('public.terminated', compact('closingDate', 'message'));
    }

    /**
     * Page déjà joué
     */
    public function dejaJoue($participant)
    {
        $participant = Participant::where('slug', $participant)->firstOrFail();
        $filmsVus = $participant->films;
        $total = Film::count();
        return view('public.already-played', compact('participant', 'filmsVus', 'total'));
    }

    /**
     * Vérifie l'état du marathon
     */
    private function checkMarathonStatus()
    {
        $openingDate = Setting::get('opening_date');
        $closingDate = Setting::get('closing_date');
        $now = now();

        if ($now < $openingDate) return redirect()->route('patience');
        if ($now > $closingDate) return redirect()->route('termine');

        return null; // ouvert
    }
}
