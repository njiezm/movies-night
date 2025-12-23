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

class AdminController extends Controller
{
    
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
        'vignette' => 'nullable|image'
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
        'vignette' => $vignettePath
    ]);

    // Créer dossier qrcodes si absent
    if (!file_exists(public_path('qrcodes'))) {
        mkdir(public_path('qrcodes'), 0755, true);
    }

    // Génération QR code avec endroid/qr-code
    $qrcodePath = "qrcodes/film-{$film->id}.png";
    
    try {
        // Utiliser endroid/qr-code
        
        
        $qrCode = QrCode::create(route('scan', $film->slug))
            ->setSize(300)
            ->setMargin(10);
            
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        $result->saveToFile(public_path($qrcodePath));
        
        $film->update(['qrcode' => $qrcodePath]);
    } catch (\Exception $e) {
        // En cas d'erreur avec la génération du QR code, on continue sans
        \Log::error('Erreur lors de la génération du QR code: ' . $e->getMessage());
    }

    return redirect()->route('admin.films')->with('success', 'Film ajouté avec succès !');
}

    public function editFilm(Film $film)
    {
        return view('admin.films.edit', compact('film'));
    }

    public function updateFilm(Request $request, Film $film)
    {
        $request->validate([
            'title'=>'required',
            'description'=>'nullable',
            'vignette'=>'nullable|image'
        ]);

        $slug = Str::slug($request->title);

        $film->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'slug'=>$slug
        ]);

        if($request->hasFile('vignette')){
            $film->vignette = $request->file('vignette')->store('vignettes','public');
            $film->save();
        }

        return redirect()->route('admin.films')->with('success','Film mis à jour !');
    }

    public function deleteFilm(Film $film)
    {
        $film->delete();
        return redirect()->route('admin.films')->with('success','Film supprimé !');
    }

    // --- DOTATIONS ---
    public function dotations()
    {
        $dotations = Dotation::all();
        return view('admin.dotations.index', compact('dotations'));
    }

    public function createDotation()
    {
        return view('admin.dotations.create');
    }

    public function storeDotation(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'dotationdate'=>'required|date'
        ]);

        Dotation::create($request->all());
        return redirect()->route('admin.dotations')->with('success','Dotation ajoutée !');
    }

    public function editDotation(Dotation $dotation)
    {
        return view('admin.dotations.edit', compact('dotation'));
    }

    public function updateDotation(Request $request, Dotation $dotation)
    {
        $request->validate([
            'title'=>'required',
            'dotationdate'=>'required|date'
        ]);

        $dotation->update($request->all());
        return redirect()->route('admin.dotations')->with('success','Dotation mise à jour !');
    }

    public function deleteDotation(Dotation $dotation)
    {
        $dotation->delete();
        return redirect()->route('admin.dotations')->with('success','Dotation supprimée !');
    }

    // --- STATISTIQUES ---
    public function stats()
    {
        $totalParticipants = Participant::count();
        $totalFilms = Film::count();
        $films = Film::withCount('participants')->get();
        $ranking = Participant::withCount('films')->orderByDesc('films_count')->get();

        return view('admin.stats', compact('totalParticipants','totalFilms','films','ranking'));
    }

    // ... autres méthodes ...

// --- TIRAGES AU SORT ---
public function tirages()
{
    $tirages = Tirage::with('dotation')->get();
    $dotations = Dotation::all(); // Ajoutez cette ligne
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

public function drawTirage(Request $request, Tirage $tirage)
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
    return response()->json($film);
}

public function getTirageData(Tirage $tirage)
{
    return response()->json($tirage);
}
}