@extends('layouts.app')

@section('title','Connexion Express')

@section('content')
<link href="{{ asset('css/madiana-connexion.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5 col-12 main_view">
            <!-- Header -->
            <div class="row justify-content-center">
                <div class="col">
                    <img src="{{ asset('images/madiana/header-connexion.jpg') }}" class="shadow img-fluid header-img" alt="Connexion Marathon">
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row justify-content-center">
                <div class="col-10 card_red">
                    <div class="p-4">
                        <!-- Titre et accroche -->
                        <div class="row justify-content-center">
                            <div class="col-11 text-center mg-top-10">
                                <h2 class="text-white mb-3">Connexion Express</h2>
                                <p class="text-white">Accédez rapidement à votre progression dans le marathon</p>
                            </div>
                        </div>

                        @if($film)
                            <!-- Section : Film scanné -->
                            <div class="row justify-content-center mb-4">
                                <div class="col-11 text-center">
                                    <p class="text-warning lead">Pour valider votre visionnage du film :</p>
                                    <div class="film-scanned">
                                        <img src="{{ asset('storage/'.$film->vignette) }}" class="img-fluid rounded shadow-lg" alt="{{ $film->title }}">
                                        <h3 class="text-white mt-3">{{ $film->title }}</h3>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Formulaire de connexion -->
                        <div style="border-radius: 50px; background: rgba(0, 0, 0, 0.7); padding:40px;" class="row justify-content-center">
                            <div class="row justify-content-center">
                                <div class="col-12 text-center mg-top-10 mg-bottom-20">
                                    <h3 class="text-white">Entrez votre numéro de téléphone</h3>
                                </div>
                            </div>

                            @if(session('error'))
                                <div class="alert alert-danger mb-3">
                                    {{ session('error') }}
                                </div>
                            @endif
                            
                            @if($errors->any())
                                <div class="alert alert-danger mb-3">
                                    @foreach($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ route('connexion.express.post') }}">
                                @csrf
                                <input type="hidden" name="film_slug" value="{{ request('film_slug') }}">
                                
                                <div class="row p-2 mg-top-10">
                                    <div class="col mg-top-5">
                                        <input type="tel" class="form-control text-center mg-top-5 rounded-pill" name="telephone" placeholder="Numéro de téléphone" required/>
                                    </div>
                                </div>
                                
                                <div class="row justify-content-center mt-4">
                                    <div class="col-8 col-sm-4 mg-top-5 text-center mg-bottom-40">
                                        <button type="submit" class="btn btn-danger btn-lg rounded-pill px-4">Connexion</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <p class="text-white">Pas encore inscrit ? 
                                @if(request('film_slug'))
                                    <a href="{{ route('inscription', ['source' => 'qr_scan']) }}?film_slug={{ request('film_slug') }}" class="text-warning">Inscrivez-vous</a>
                                @else
                                    <a href="{{ route('inscription') }}" class="text-warning">Inscrivez-vous</a>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <img src="{{ asset('images/madiana/footer-connexion.jpg') }}" class="img-fluid footer-img" alt="Cinéma Madiana">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection