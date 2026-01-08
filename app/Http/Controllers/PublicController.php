<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
use App\Models\Setting;
use App\Models\Base\Genesys;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PublicController extends Controller
{
    /**
     * Affiche la page d'inscription
     */
    public function showInscription($source = null)
    {
        if ($redirect = $this->checkMarathonStatus()) {
            return $redirect;
        }

        return view('public.inscription', compact('source'));
    }

    /**
     * Enregistre un participant
     */
    public function storeInscription(Request $request)
    {
        $minAge = Setting::get('min_age', 14);

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'zipcode' => 'nullable|string|max:10',
            'age' => "required|integer|min:$minAge|max:120",
            'optin' => 'required|boolean',
        ]);

        if ($request->age < $minAge) {
            return back()->with('error', "Vous devez avoir au moins $minAge ans.")->withInput();
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

        // Optin
        $optin = (int) $request->optin;
        $bysms = false;
        $byemail = false;

        if ($optin === 1 && $request->has('contact_method')) {
            $bysms   = in_array($request->contact_method, [1, 3]);
            $byemail = in_array($request->contact_method, [2, 3]);
        }

        // Source
        $source = $request->boolean('from_qr_scan') ? 'salle' : ($request->source ?? 'web');

        // Slug sécurisé
        $slug = Genesys::GenCodeAlphaNum(20);

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
            'is_over_14' => true,
        ]);

        // Redirection post inscription
        if ($request->boolean('from_qr_scan') && $request->filled('film_slug')) {
            return redirect()->route('mes.films', [
                'participant' => $participant->slug,
                'film_slug' => $request->film_slug
            ])->with('success', 'Inscription réussie !');
        }

        return redirect()->route('rendez.vous')->with('success', 'Inscription réussie !');
    }

    /**
     * Page rendez-vous
     */
    public function rendezVous()
    {
        if ($redirect = $this->checkMarathonStatus()) {
            return $redirect;
        }

        $films = Film::orderBy('title')->get();
        return view('public.rendez-vous', compact('films'));
    }

    /**
     * Films d’un participant (scan inclus)
     */
    public function mesFilms($participantSlug)
    {
        if ($redirect = $this->checkMarathonStatus()) {
            return $redirect;
        }

        $participant = Participant::where('slug', $participantSlug)->firstOrFail();
        $filmsVus = $participant->films;
        $total = Film::count();
        $film = null;

        $filmSlug = request('film_slug');

        if ($filmSlug) {
            $film = Film::where('slug', $filmSlug)->first();

            if ($film) {
                $now = Carbon::now()->startOfDay();

                if (
                    ($film->start_date && $now->lt(Carbon::parse($film->start_date))) ||
                    ($film->end_date && $now->gt(Carbon::parse($film->end_date)))
                ) {
                    return redirect()
                        ->route('mes.films', ['participant' => $participant->slug])
                        ->with('error', 'Ce film n’est pas disponible à cette date.');
                }

              
                if ($filmsVus->contains($film->id)) {
                    return redirect()->route('deja.joue', ['participant' => $participant->slug]);
                }

              
                $participant->films()->attach($film->id);
                $filmsVus = $participant->fresh()->films;
            }
        }

        return view('public.mes-films', compact('participant', 'filmsVus', 'total', 'film'));
    }

    /**
     * Connexion rapide par téléphone
     */
    public function connexionExpress(Request $request)
    {
        $request->validate(['telephone' => 'required']);
        $encryptedPhone = Genesys::Crypt($request->telephone);

        $participant = Participant::where('telephone', $encryptedPhone)->first();

        if (!$participant) {
            return redirect()->route('inscription', ['source' => 'qr_scan'])
                ->with('error', 'Numéro introuvable, veuillez vous inscrire.')
                ->with('film_slug', $request->film_slug);
        }

        $params = ['participant' => $participant->slug];
        if ($request->film_slug) $params['film_slug'] = $request->film_slug;

        return redirect()->route('mes.films', $params);
    }

    /**
     * Scan QR
     */
    public function scanQr($slug)
    {
        if ($redirect = $this->checkMarathonStatus()) {
            return $redirect;
        }

        $film = Film::where('slug', $slug)->firstOrFail();
        $now = Carbon::now()->startOfDay();


        if (
            ($film->start_date && $now->lt(Carbon::parse($film->start_date))) ||
            ($film->end_date && $now->gt(Carbon::parse($film->end_date)))
        ) {
            return redirect()->route('patience')
                ->with('message', 'Ce film n’est pas disponible actuellement.');
        }

        return view('public.scan-connexion', compact('film'));
    }

    /**
     * Pages état marathon
     */
    public function patience()
    {
        return view('public.patience', [
            'openingDate' => Setting::get('opening_date'),
            'message' => request('message')
        ]);
    }

    public function termine()
    {
        return view('public.terminated', [
            'closingDate' => Setting::get('closing_date'),
            'message' => request('message')
        ]);
    }

    /**
     * Déjà joué
     */
    public function dejaJoue($participant)
    {
        $participant = Participant::where('slug', $participant)->firstOrFail();
        return view('public.already-played', [
            'participant' => $participant,
            'filmsVus' => $participant->films,
            'total' => Film::count()
        ]);
    }

    /**
     * Vérifie état du marathon
     */
    private function checkMarathonStatus()
    {
        $now = Carbon::now();
        $openingDate = Setting::get('opening_date');
        $closingDate = Setting::get('closing_date');

        if ($openingDate && $now->lt(Carbon::parse($openingDate))) {
            return redirect()->route('patience');
        }

        if ($closingDate && $now->gt(Carbon::parse($closingDate))) {
            return redirect()->route('termine');
        }

        return null;
    }
}
