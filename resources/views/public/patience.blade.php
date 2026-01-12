@extends('layouts.app')

@section('title','Patience')

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
                <div class="col-10">
                    <div class="p-4">
                         <!-- Image de dotation -->
                        <div class="row justify-content-center">
                            <div class="col-12 text-center mg-top-20 mg-bottom-20">
                                <img src="{{ asset('images/madiana/patience.png') }}" class="img-fluid rounded" alt="Dotation">
                            </div>
                        </div>
                        
                         <div class="row justify-content-center mt-5 mb-5">
                                    <div class="col-11 col-sm-8 mg-top-5 text-center mg-bottom-40">
                                        <button type="submit" style="background: transparent; border: none;" class="btn-submit-img">
                                            <img src="{{ asset('images/madiana/selection-films.png') }}"
                                                class="img-fluid"
                                                alt="Selection de films">
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row justify-content-center mt-3">
                                    <div class="col-8 col-sm-8 mg-top-1 text-center mg-bottom-40">
                                            <img src="{{ asset('images/madiana/bonne-chance.png') }}"
                                                class="img-fluid"
                                                alt="Selection de films">
                                    </div>
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