@extends('layouts.app')

@section('title','Scan Film')

@section('content')
<link href="{{ asset('css/madiana-scan.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5 col-12 main_view">
            <!-- Header -->
            <div class="row justify-content-center">
                <div class="col">
                    <img src="{{ asset('images/madiana/header-scan.jpg') }}" class="shadow img-fluid header-img" alt="Scan Marathon">
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row justify-content-center">
                <div class="col-10 card_red">
                    <div class="p-4">
                        <!-- Message de bienvenue -->
                        <div class="row justify-content-center">
                            <div class="col-11 text-center">
                                <h1 class="text-white mb-3">Bienvenue dans le monde de l'horreur !</h1>
                                <p class="lead text-warning mb-4">Vous venez de scanner le film :</p>
                            </div>
                        </div>

                        <!-- Détails du film scanné -->
                        <div class="row justify-content-center">
                            <div class="col-10 text-center">
                                <div class="film-scanned">
                                    <img src="{{ asset('storage/'.$film->vignette) }}" class="img-fluid rounded shadow-lg" alt="{{ $film->title }}">
                                    <h2 class="text-white mt-3">{{ $film->title }}</h2>
                                    <p class="text-white-50">{{ $film->description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Appel à l'action : Connexion ou Inscription -->
                        <div class="row justify-content-center mt-4">
                            <div class="col-12 text-center">
                                <p class="text-white mb-4">Pour valider votre participation et marquer ce film comme vu, veuillez vous connecter ou vous inscrire.</p>
                            </div>
                        </div>
                        
                        <div class="row justify-content-center gx-2">
                            <div class="col-12 col-sm-6 mb-2">
                                <a href="{{ route('connexion.express') }}?film_slug={{ $film->slug }}" class="btn btn-outline-light btn-lg w-100 rounded-pill">
                                    <i class="fas fa-sign-in-alt"></i> J'ai déjà un compte
                                </a>
                            </div>
                            <div class="col-12 col-sm-6 mb-2">
                                {{-- IMPORTANT: On force la source à 'salle' car l'inscription vient d'un scan --}}
                                <a href="{{ route('inscription', ['source' => 'salle']) }}?from_qr_scan=1&film_slug={{ $film->slug }}" class="btn btn-danger btn-lg w-100 rounded-pill">
                                    <i class="fas fa-user-plus"></i> Je m'inscris
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <img src="{{ asset('images/madiana/footer-scan.jpg') }}" class="img-fluid footer-img" alt="Cinéma Madiana">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection