<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function accueil()
    {
        return view('public.accueil');
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
        ]);

        // Si l'inscription vient d'un scan QR code, rediriger vers la page des films
        if ($request->has('from_qr_scan') && $request->has('film_slug')) {
            return redirect()->route('mes.films', ['participant' => $participant->slug, 'film_slug' => $request->film_slug])
                ->with('success', 'Inscription réussie !');
        }

        // Sinon, rediriger vers la page de rendez-vous
        return redirect()->route('rendez.vous');
    }

    public function showConnexionExpress()
    {
        return view('public.connexion-express');
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

    public function rendezVous()
    {
        return view('public.rendez-vous');
    }
}