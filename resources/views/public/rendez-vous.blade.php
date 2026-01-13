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

                              <div class="row justify-content-center mt-4">
                                <div class="col-12 col-sm-8 mg-top-5 text-center mg-bottom-40">
                                    <a href="#" class="d-inline-block btn-submit-img">
                                        <img src="{{ asset('images/madiana/selection-films.png') }}"
                                            class="img-fluid"
                                            alt="Sélection de films">
                                    </a>
                                </div>
                            </div>

                                
                                <div class="row justify-content-center mt-1">
                                    <div class="col-9 col-sm-8 mg-top-1 text-center">
                                            <img src="{{ asset('images/madiana/bientot.png') }}"
                                                class="img-fluid"
                                                alt="Selection de films">
                                    </div>
                                </div>
                                <!--h2 class="text-white mb-3">Rendez-vous dans votre cinéma Madiana</-h2-->
                                <!--p class="text-white">Pour le marathon de films d'horreur !</!--p-->
                            </div>
                        </div>
   
                        <!--div class="text-center mt-4 p-3 cta-block">
                            <p class="mb-2 text-white">Les QR codes seront disponibles uniquement dans le cinéma.</p>
                            <p class="mb-3 text-white">Inscrivez-vous pour participer à la course !</p>
                            <a href="{{ url('/inscription') }}" class="btn btn-danger btn-lg rounded-pill px-4">S'inscrire maintenant</a>
                        </!--div-->
                    </div>
                </div>
            </div>
             <div class="row justify-content-center">
                <div class="col-12">
                    <div class="p-4">
                        <div class="row justify-content-center">
                            <div class="col-12 text-center mg-top-10">
                                <h2>
                                                                         <a href="{{ asset('pdfs/POLITIQUE_DE_CONFIDENTIALITE_MADIANA__Marathondelhorreur_2026.pdf') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    style="color: #ffffff; text-decoration: underline; font-size:20px;">
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
@endsection