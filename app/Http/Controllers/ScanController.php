<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
use App\Models\Setting;
use App\Models\Base\Genesys;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function scan(Film $film, Request $request)
    {
        // Vérifier si le marathon est ouvert
        $openingDate = Setting::get('opening_date');
        $closingDate = Setting::get('closing_date');
        $now = now();
        
        if ($now < $openingDate) {
            return redirect()->route('patience')->with('message', 'Ce film n\'est pas encore disponible.');
        }
        
        if ($now > $closingDate) {
            return redirect()->route('termine')->with('message', 'Ce film n\'est plus disponible.');
        }
        
        // Vérifier si le film est dans la période de validité
        if ($film->start_date && $now < $film->start_date) {
            return redirect()->route('patience')->with('message', 'Ce film n\'est pas encore disponible.');
        }
        
        if ($film->end_date && $now > $film->end_date) {
            return redirect()->route('termine')->with('message', 'Ce film n\'est plus disponible.');
        }
        
        // Vérifier si le participant a l'âge requis
        $minAge = Setting::get('min_age', 14);
        
        // Récupérer le participant par téléphone ou slug
        $participant = null;
        
        if ($request->has('telephone')) {
            $encryptedPhone = Genesys::Crypt($request->telephone);
            $participant = Participant::where('telephone', $encryptedPhone)->first();
        } elseif ($request->has('participant_slug')) {
            $participant = Participant::where('slug', $request->participant_slug)->first();
        }
        
        if (!$participant) {
            return redirect()->route('inscription')->with('error', 'Participant non trouvé. Veuillez vous inscrire.');
        }
        
        // Vérifier si le participant a l'âge requis
        if (!$participant->is_over_14 && $minAge >= 14) {
            return redirect()->route('inscription')->with('error', 'Vous devez avoir au moins ' . $minAge . ' ans pour participer.');
        }

        if (!$participant->films()->where('film_id', $film->id)->exists()) {
            $participant->films()->attach($film->id);
        }

        $seen = $participant->films()->count();
        $total = Film::count();
        
        // Déchiffrement des données pour l'affichage
        $decryptedParticipant = [
            'firstname' => $participant->firstname,
            'lastname' => $participant->lastname,
        ];

        return view('scan.success', compact('film', 'seen', 'total', 'decryptedParticipant'));
    }
    
    /**
     * Recherche un participant par téléphone avec déchiffrement
     */
    public function searchParticipant(Request $request)
    {
        $request->validate([
            'telephone' => 'required|string',
        ]);
        
        // Chiffrement du téléphone pour la recherche
        $encryptedPhone = Genesys::Crypt($request->telephone);
        
        $participant = Participant::where('telephone', $encryptedPhone)->first();
        
        if (!$participant) {
            return response()->json(['error' => 'Participant non trouvé'], 404);
        }
        
        // Vérifier si le participant a l'âge requis
        $minAge = Setting::get('min_age', 14);
        if (!$participant->is_over_14 && $minAge >= 14) {
            return response()->json(['error' => 'Le participant doit avoir au moins ' . $minAge . ' ans'], 403);
        }
        
        // Déchiffrement des données pour la réponse
        $decryptedParticipant = [
            'id' => $participant->id,
            'slug' => $participant->slug,
            'firstname' => $participant->firstname,
            'lastname' => $participant->lastname,
            'telephone' => $participant->telephone,
            'email' => $participant->email ? $participant->email : null,
            'films_count' => $participant->films()->count(),
        ];
        
        return response()->json($decryptedParticipant);
    }
}