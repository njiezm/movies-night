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

                     

                        <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <!--div class="row justify-content-center">
                                    <div class="col-8 text-center mb-4">
                                        <img src="{{ asset('images/madiana/express-register.png') }}" class="img-fluid">
                                    </div>
                                 </!--div-->
                                
                                 <div class="row justify-content-center mt-4">
                                    <div class="col-9 col-sm-6 text-center mg-bottom-40">
                                <a href="{{ route('inscription', ['source' => 'salle']) }}?from_qr_scan=1&film_slug={{ $film->slug }}"
                                class="image-btn image-btn-danger">
                                    <img src="{{ asset('images/madiana/icon-inscription.png') }}"
                                        alt="Bouton Inscription">
                                </a>
                                </div>
                        </div>
                            </div>
                        </div>

                        <!-- Formulaire de connexion express -->
                        <div class="row justify-content-center mb-4 mt-4">
                            <div class="row justify-content-center">
                                <div class="col-12 text-center mg-top-10 mg-bottom-20">
                                  <div class="row justify-content-center">
                                    <div class="col-10 text-center mb-4 mt-4">
                                        <img src="{{ asset('images/madiana/express-login.png') }}" class="img-fluid">
                                    </div>
                                 </div>
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
                                        <input type="tel" class="form-control text-center mg-top-5 rounded-pill input-white-big" style="background: rgba(255, 255, 255, 0.20); color:white;" name="telephone" placeholder="Numéro de téléphone" required/>
                                    </div>
                                </div>
                                
                                <div class="row justify-content-center mt-4">
                                    <div class="col-7 col-sm-6 mg-top-5 text-center mg-bottom-40">
                                        <a href="#" onclick="document.getElementById('connexionForm').submit(); return false;" class="image-btn">
                                            <img src="{{ asset('images/madiana/icon-connexion.png') }}"
                                                alt="Bouton Connexion">
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>        
                    </div>
                </div>
            </div>
                <div class="row justify-content-center">
                <div class="col-12">
                    <div class="p-4">
                        <div class="row justify-content-center">
                            <div class="col-12 text-center mg-top-10">
                                <h2>
                                    <a href="https://lien.fr"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    style="color: #ffffff; text-decoration: underline; font-size:20px">
                                        Politique de confidentialité
                                    </a>
                                </h2>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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