<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PublicController;

// FRONT PUBLIC
Route::get('/', [PublicController::class, 'accueil'])->name('accueil');
Route::get('/inscription/{source?}', [PublicController::class, 'showInscription'])->name('inscription');
Route::post('/inscription', [PublicController::class, 'storeInscription'])->name('inscription.store');
Route::get('/connexion/express', [PublicController::class, 'showConnexionExpress'])->name('connexion.express');
Route::post('/connexion/express', [PublicController::class, 'connexionExpress'])->name('connexion.express.post');
Route::get('/scan/{slug}', [PublicController::class, 'scanQr'])->name('scan');
Route::get('/mes-films/{participant}', [PublicController::class, 'mesFilms'])->name('mes.films');
Route::get('/rendez-vous', [PublicController::class, 'rendezVous'])->name('rendez.vous');

// ADMIN
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'stats'])->name('admin.stats');
    Route::get('/films', [AdminController::class, 'films'])->name('admin.films');
    Route::get('/films/create', [AdminController::class, 'createFilm'])->name('admin.films.create');
    Route::post('/films', [AdminController::class, 'storeFilm'])->name('admin.films.store');
    Route::get('/films/{film}/edit', [AdminController::class, 'editFilm'])->name('admin.films.edit');
    Route::post('/films/{film}', [AdminController::class, 'updateFilm'])->name('admin.films.update');
    Route::delete('/films/{film}', [AdminController::class, 'deleteFilm'])->name('admin.films.delete');
    Route::get('/films/{film}/data', [AdminController::class, 'getFilmData'])->name('admin.films.data');
    
    Route::get('/tirages', [AdminController::class, 'tirages'])->name('admin.tirages');
    Route::get('/tirages/create', [AdminController::class, 'createTirage'])->name('admin.tirages.create');
    Route::post('/tirages', [AdminController::class, 'storeTirage'])->name('admin.tirages.store');
    Route::get('/tirages/{tirage}/edit', [AdminController::class, 'editTirage'])->name('admin.tirages.edit');
    Route::post('/tirages/{tirage}', [AdminController::class, 'updateTirage'])->name('admin.tirages.update');
    Route::delete('/tirages/{tirage}', [AdminController::class, 'deleteTirage'])->name('admin.tirages.delete');
    Route::post('/tirages/{tirage}/draw', [AdminController::class, 'drawTirage'])->name('admin.tirages.draw');
    Route::get('/tirages/{tirage}/data', [AdminController::class, 'getTirageData'])->name('admin.tirages.data');
    
    Route::get('/dotations', [AdminController::class, 'dotations'])->name('admin.dotations');
    Route::get('/dotations/create', [AdminController::class, 'createDotation'])->name('admin.dotations.create');
    Route::post('/dotations', [AdminController::class, 'storeDotation'])->name('admin.dotations.store');
    Route::get('/dotations/{dotation}/edit', [AdminController::class, 'editDotation'])->name('admin.dotations.edit');
    Route::post('/dotations/{dotation}', [AdminController::class, 'updateDotation'])->name('admin.dotations.update');
    Route::delete('/dotations/{dotation}', [AdminController::class, 'deleteDotation'])->name('admin.dotations.delete');
});