@extends('layouts.app')

@section('title','Deja joué')

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
                                <img src="{{ asset('images/madiana/deja-joe.png') }}" class="img-fluid rounded" alt="Dotation">
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </div>
            </div>

              <div class="row justify-content-center mt-4">
                                <div class="col-8 col-sm-8 mg-top-5 text-center mg-bottom-40">
                                    <a href="https://madiana.com/events/12746-marathon-de-lhorreur-a-madiana/" target="_blank" class="d-inline-block btn-submit-img">
                                        <img src="{{ asset('images/madiana/selection-films.png') }}"
                                            class="img-fluid"
                                            alt="Sélection de films">
                                    </a>
                                </div>
                            </div>
                                
                                <div class="row justify-content-center mt-3">
                                    <div class="col-7 col-sm-3 mg-top-1 text-center mg-bottom-40">
                                            <img src="{{ asset('images/madiana/bientot.png') }}"
                                                class="img-fluid"
                                                alt="Selection de films">
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