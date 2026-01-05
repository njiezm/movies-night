<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Base\Genesys;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class FilmController extends Controller
{
    // Liste films
    public function index()
    {
        $films = Film::withCount('participants')->get();
        return view('admin.films.index', compact('films'));
    }

    // Formulaire création
    public function create()
    {
        return view('admin.films.create');
    }

    // Stockage film
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'vignette' => 'nullable|image|max:2048',
        ]);

        // Génération d'un slug sécurisé avec Genesys
        $slug = Genesys::GenCodeAlphaNum(10);
        
        $vignettePath = null;

        if ($request->hasFile('vignette')) {
            $vignettePath = $request->file('vignette')->store('vignettes', 'public');
        }

        $film = Film::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => $slug,
            'vignette' => $vignettePath,
        ]);

        // QR code
        $qrcodePath = "qrcodes/film-{$film->id}.png";
        QrCode::format('png')
            ->size(300)
            ->generate(route('scan', $film->slug), public_path($qrcodePath));

        $film->update(['qrcode' => $qrcodePath]);

        return redirect()->route('admin.films.index')->with('success', 'Film ajouté.');
    }

    // Formulaire édition
    public function edit(Film $film)
    {
        return view('admin.films.edit', compact('film'));
    }

    // Update
    public function update(Request $request, Film $film)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'vignette' => 'nullable|image|max:2048',
        ]);

        $slug = Genesys::GenSlugCode($request->title);
        $film->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => $slug,
        ]);

        if ($request->hasFile('vignette')) {
            $film->vignette = $request->file('vignette')->store('vignettes', 'public');
            $film->save();
        }

        return redirect()->route('admin.films.index')->with('success', 'Film mis à jour.');
    }

    // Suppression
    public function destroy(Film $film)
    {
        $film->delete();
        return redirect()->route('admin.films.index')->with('success', 'Film supprimé.');
    }
}