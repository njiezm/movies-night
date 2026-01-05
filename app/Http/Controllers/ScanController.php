<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Participant;
use App\Models\Base\Genesys;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function scan(Film $film, Request $request)
    {
        $participant = Participant::first(); 

        if (!$participant->films()->where('film_id', $film->id)->exists()) {
            $participant->films()->attach($film->id);
        }

        $seen = $participant->films()->count();
        $total = Film::count();
        
        // Déchiffrement des données pour l'affichage
        $decryptedParticipant = [
            'firstname' => Genesys::Decrypt($participant->firstname),
            'lastname' => Genesys::Decrypt($participant->lastname),
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
        
        // Déchiffrement des données pour la réponse
        $decryptedParticipant = [
            'id' => $participant->id,
            'slug' => $participant->slug,
            'firstname' => Genesys::Decrypt($participant->firstname),
            'lastname' => Genesys::Decrypt($participant->lastname),
            'telephone' => Genesys::Decrypt($participant->telephone),
            'email' => $participant->email ? Genesys::Decrypt($participant->email) : null,
            'films_count' => $participant->films()->count(),
        ];
        
        return response()->json($decryptedParticipant);
    }
}