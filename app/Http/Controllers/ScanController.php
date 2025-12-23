<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;
use App\Models\Participant;

class ScanController extends Controller
{
    public function scan(Film $film, Request $request)
    {
        // participant "dummy" pour test (pas d'auth)
        $participant = Participant::first(); 

        if (!$participant->films()->where('film_id', $film->id)->exists()) {
            $participant->films()->attach($film->id);
        }

        $seen = $participant->films()->count();
        $total = Film::count();

        return view('scan.success', compact('film', 'seen', 'total'));
    }
}
