@extends('layouts.app')

@section('title','Rendez-vous Marathon')

@section('content')
<link href="{{ asset('css/madiana-rendezvous.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5 col-12 main_view">
            <!-- Header -->
            <div class="row justify-content-center">
                <div class="col">
                    <img src="{{ asset('images/madiana/header.png') }}" class="img-fluid" alt="Marathon de Films d'Horreur">
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="p-4">
                        <!-- Titre et accroche -->
                        <div class="row justify-content-center">
                            <div class="col-11 text-center mg-top-10">
                                  <!-- Image de dotation -->
                        <div class="row justify-content-center">
                            <div class="col-12 text-center mg-top-20 mg-bottom-20">
                                <img src="{{ asset('images/madiana/dotation-rdv.png') }}" class="img-fluid rounded" alt="Dotation">
                            </div>
                        </div>
                                <!--h2 class="text-white mb-3">Rendez-vous dans votre cinéma Madiana</-h2-->
                                <!--p class="text-white">Pour le marathon de films d'horreur !</!--p-->
                            </div>
                        </div>

                        <!-- Informations clés de l'événement -->
                        <div class="row text-center mb-4 event-info">
                            <div class="col-12">
                                <!--h3 class="h5 text-warning">Une nuit de frissons vous attend !</-h3-->
                                <p>Scannez les QR codes dans le cinéma pour participer à la course.</p>
                            </div>
                            <div class="col-4 mt-3">
                                <i class="fas fa-calendar-alt fa-2x text-warning mb-2"></i>
                                <h5>Date</h5>
                                <p class="text-white">du 18 Janvier au 20 Juin 2026</p>
                            </div>
                            <div class="col-4 mt-3">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h5>Heure</h5>
                                <p class="text-white">Tous les soirs</p>
                            </div>
                            <div class="col-4 mt-3">
                                <i class="fas fa-map-marker-alt fa-2x text-warning mb-2"></i>
                                <h5>Lieu</h5>
                                <p class="text-white">Cinéma Madiana</p>
                            </div>
                        </div>

                        <!-- Liste des films à l'affiche -->
                        <div class="text-center mb-4">
                            <h3 class="text-white">Les films à l'affiche</h3>
                        </div>

                        <div class="row">
                            @forelse ($films as $film)
                                <div class="col-6 mb-3">
                                    <div class="film-card-custom">
                                        <a href="#" title="Voir les détails de {{ $film->title }}">
                                            <img src="{{ asset('storage/'.$film->vignette) }}" class="film-poster" alt="Affiche du film {{ $film->title }}">
                                        </a>
                                        <div class="film-title mt-2">
                                            <h5>{{ $film->title }}</h5>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-center text-white">La liste des films sera bientôt disponible.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Appel à l'action final -->
                        <!--div class="text-center mt-4 p-3 cta-block">
                            <p class="mb-2 text-white">Les QR codes seront disponibles uniquement dans le cinéma.</p>
                            <p class="mb-3 text-white">Inscrivez-vous pour participer à la course !</p>
                            <a href="{{ url('/inscription') }}" class="btn btn-danger btn-lg rounded-pill px-4">S'inscrire maintenant</a>
                        </!--div-->
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