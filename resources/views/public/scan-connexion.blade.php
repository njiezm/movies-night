@extends('layouts.app')

@section('title','Scan Film & Connexion')

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
                        <!-- Message de bienvenue -->
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

                        <!-- Détails du film scanné -->
                        <div class="row justify-content-center">
                            <div class="col-10 text-center">
                                <div class="film-scanned">
                                    <img src="{{ asset('storage/'.$film->vignette) }}" class="img-fluid rounded" alt="{{ $film->title }}">
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

                         <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <p class="text-white mb-4">Pas encore inscrit ?</p>
                                <a href="{{ route('inscription', ['source' => 'salle']) }}?from_qr_scan=1&film_slug={{ $film->slug }}"
                                class="image-btn image-btn-danger">
                                    <img src="{{ asset('images/madiana/icon-inscription.png') }}"
                                        alt="Bouton Inscription">
                                </a>
                            </div>
                        </div>

                        
                        
                        <!-- Formulaire de connexion express -->
                        <div class="row justify-content-center mb-4 mt-4">
                            <div class="row justify-content-center">
                                <div class="col-12 text-center mg-top-10 mg-bottom-20">
                                    <h3 class="text-white">Connexion Express</h3>
                                    <p class="text-white-50">Entrez votre numéro de téléphone</p>
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
                            
                            <form method="POST" action="{{ route('connexion.express.post') }}" id="connexionForm">
                                @csrf
                                <input type="hidden" name="film_slug" value="{{ $film->slug }}">
                                
                                <div class="row p-2 mg-top-10">
                                    <div class="col mg-top-5">
                                        <input type="tel" class="form-control text-center mg-top-5 rounded-pill" name="telephone" placeholder="Numéro de téléphone" required/>
                                    </div>
                                </div>
                                
                                <div class="row justify-content-center mt-4">
                                    <div class="col-12 col-sm-6 mg-top-5 text-center mg-bottom-40">
                                        <!-- Image bouton de connexion -->
                                        <a href="#" onclick="document.getElementById('connexionForm').submit(); return false;" class="image-btn">
                                            <img src="{{ asset('images/madiana/icon-connexion.png') }}"
                                                alt="Bouton Connexion">
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Bouton d'inscription -->
                       
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ajout d'une validation simple avant la soumission
    document.getElementById('connexionForm').addEventListener('submit', function(e) {
        const telephone = document.querySelector('input[name="telephone"]').value;
        
        if (!telephone || telephone.trim() === '') {
            e.preventDefault();
            alert('Veuillez entrer votre numéro de téléphone');
            return false;
        }
    });
});
</script>
@endsection
@endsection