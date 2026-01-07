@extends('layouts.app')

@section('title','Mes Films')

@section('content')
<link href="{{ asset('css/madiana-mes-films.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5 col-12 main_view">
            <!-- Header -->
            <div class="row justify-content-center">
                <div class="col">
                    <img src="{{ asset('images/madiana/header.png') }}" class="img-fluid header-img" alt="Mon Marathon">
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row justify-content-center">
                <div class="col-10 card_red">
                    <div class="p-4">
                        <!-- Titre et accroche -->
                        <div class="row justify-content-center">
                            <div class="col-11 text-center mg-top-10">
                                <h1>Bonjour {{ $participant->firstname }}</h1>
                                <p class="text-warning lead">Votre progression dans le marathon</p>
                            </div>
                        </div>

                        <!-- Barre de progression -->
                        <div class="row justify-content-center mb-4">
                            <div class="col-12">
                                <div class="custom-progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ ($total > 0) ? ($filmsVus->count() / $total) * 100 : 0 }}%;" aria-valuenow="{{ $filmsVus->count() }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                                        {{ $filmsVus->count() }} / {{ $total }} Films
                                    </div>
                                </div>
                                <p class="text-center text-white mt-2">
                                    @if($filmsVus->count() === $total && $total > 0)
                                        <i class="fas fa-trophy text-warning"></i> Félicitations, vous avez terminé le marathon !
                                    @else
                                        Continuez votre quête de survie !
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Liste des films vus -->
                        <div class="row justify-content-center">
                            <div class="col-11">
                                <h3 class="text-white mb-3 text-center">Films que vous avez vu :</h3>
                                <div class="row">
                                    @forelse($filmsVus as $film)
                                        <div class="col-6 mb-3">
                                            <div class="seen-film-card">
                                                <div class="image-container">
                                                    <img src="{{ asset('storage/'.$film->vignette) }}" class="film-poster" alt="{{ $film->title }}">
                                                    <div class="seen-overlay">
                                                        <i class="fas fa-check-circle"></i>
                                                    </div>
                                                </div>
                                                <div class="film-title mt-2">
                                                    <h5>{{ $film->title }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-center text-white-50">Vous n'avez pas encore scanné de film. Allez au cinéma pour commencer !</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        <!-- Appel à l'action final -->
                        <div class="text-center mt-4">
                            <p class="text-white">Bon film et bon courage !</p>
                            <a href="{{ route('rendez.vous') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                <i class="fas fa-home"></i> Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <img src="{{ asset('images/madiana/footer.png') }}" class="img-fluid" alt="Cinéma Madiana">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection