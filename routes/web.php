<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PublicController;

// Routes d'administration avec authentification par code


Route::prefix('admin')->group(function () {
    // Page de connexion
    Route::get('/login', function() {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.stats');
        }
        return view('admin.login');
    })->name('admin.login.form');
    
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Routes protégées
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminController::class, 'stats'])->name('admin.stats');
        
        // Réglages
        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::put('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
        
        // Films
        Route::get('/films', [AdminController::class, 'films'])->name('admin.films');
        Route::get('/films/create', [AdminController::class, 'createFilm'])->name('admin.films.create');
        Route::post('/films', [AdminController::class, 'storeFilm'])->name('admin.films.store');
        Route::get('/films/{film}/edit', [AdminController::class, 'editFilm'])->name('admin.films.edit');
        Route::put('/films/{film}', [AdminController::class, 'updateFilm'])->name('admin.films.update');
        Route::delete('/films/{film}', [AdminController::class, 'deleteFilm'])->name('admin.films.delete');
        Route::get('/films/{film}/data', [AdminController::class, 'getFilmData'])->name('admin.films.data');
        Route::get('/films/p/{idfilm}', [AdminController::class, 'getparticipantsFilm'])->name('admin.film.participants');
        Route::get('/participants', [AdminController::class,'allParticipants']);

        // Tirages
        Route::get('/tirages', [AdminController::class, 'tirages'])->name('admin.tirages');
        Route::get('/tirages/create', [AdminController::class, 'createTirage'])->name('admin.tirages.create');
        Route::post('/tirages', [AdminController::class, 'storeTirage'])->name('admin.tirages.store');
        Route::get('/tirages/{tirage}/edit', [AdminController::class, 'editTirage'])->name('admin.tirages.edit');
        Route::put('/tirages/{tirage}', [AdminController::class, 'updateTirage'])->name('admin.tirages.update');
        Route::delete('/tirages/{tirage}', [AdminController::class, 'deleteTirage'])->name('admin.tirages.delete');
        Route::post('/tirages/{tirage}/draw', [AdminController::class, 'drawTirage'])->name('admin.tirages.draw');
        Route::get('/tirages/{tirage}/data', [AdminController::class, 'getTirageData'])->name('admin.tirages.data');
        Route::post('/tirages/create-big-tas', [AdminController::class, 'createBigTas'])->name('admin.tirages.createBigTas');
        
        // Dotations
        Route::get('/dotations', [AdminController::class, 'dotations'])->name('admin.dotations');
        Route::get('/dotations/create', [AdminController::class, 'createDotation'])->name('admin.dotations.create');
        Route::post('/dotations', [AdminController::class, 'storeDotation'])->name('admin.dotations.store');
        Route::get('/dotations/{dotation}/edit', [AdminController::class, 'editDotation'])->name('admin.dotations.edit');
        Route::put('/dotations/{dotation}', [AdminController::class, 'updateDotation'])->name('admin.dotations.update');
        Route::delete('/dotations/{dotation}', [AdminController::class, 'deleteDotation'])->name('admin.dotations.delete');
    });
});

// FRONT PUBLIC
Route::get('/', [PublicController::class, 'showInscription'])->name('inscription');
Route::get('/inscription/{source?}', [PublicController::class, 'showInscription'])->name('inscription');
Route::post('/inscription', [PublicController::class, 'storeInscription'])->name('inscription.store');
Route::post('/connexion/express', [PublicController::class, 'connexionExpress'])->name('connexion.express.post');
Route::get('/scan/{slug}', [PublicController::class, 'scanQr'])->name('scan');
Route::get('/mes-films/{participant}', [PublicController::class, 'mesFilms'])->name('mes.films');
Route::get('/rendez-vous', [PublicController::class, 'rendezVous'])->name('rendez.vous');

// Pages d'état du marathon
Route::get('/patience', [PublicController::class, 'patience'])->name('patience');
Route::get('/termine', [PublicController::class, 'termine'])->name('termine');
//Route::get('/deja-joue', [PublicController::class, 'dejaJoue'])->name('deja.joue');
Route::get('/deja-joue/{participant}', [PublicController::class, 'dejaJoue'])->name('deja.joue');
