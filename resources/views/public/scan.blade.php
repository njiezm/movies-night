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
                    <img src="{{ asset('images/madiana/header.png') }}" class="img-fluid header-img" alt="Scan Marathon">
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="p-4">
                        <div class="row justify-content-center">
                            <div class="col-11 text-center">
                                <!-- Image de dotation -->
                        <div class="row justify-content-center">
                            <div class="col-12 text-center mg-top-20 mg-bottom-20">
                                <img src="{{ asset('images/madiana/dotation-express.png') }}" class="img-fluid rounded mb-4" alt="Dotation">
                            </div>
                        </div>
                                
                            </div>
                        </div>

                       
                        
                       <div class="row justify-content-center gx-2">
                        <div class="col-12 col-sm-6 mb-2">
                            <a href="{{ route('connexion.express') }}?film_slug={{ $film->slug }}"
                            class="image-btn">
                                <img src="{{ asset('images/madiana/icon-connexion.png') }}"
                                    alt="Bouton Connexion">
                            </a>
                        </div>

                        <div class="col-12 col-sm-6 mb-2">
                            <a href="{{ route('inscription', ['source' => 'salle']) }}?from_qr_scan=1&film_slug={{ $film->slug }}"
                            class="image-btn image-btn-danger">
                                <img src="{{ asset('images/madiana/icon-inscription.png') }}"
                                    alt="Bouton Inscription">
                            </a>
                        </div>
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