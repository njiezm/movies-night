@extends('layouts.app')

@section('title','Terminé')

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
                <div class="col-12">
                    <div class="p-4">
                         <!-- Image de dotation -->
                        <div class="row justify-content-center">
                            <div class="col-12 text-center mg-top-20 mg-bottom-20">
                                <img src="{{ asset('images/madiana/end.png') }}" class="img-fluid rounded" alt="Dotation">
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
                                    style="color: #ffffff; text-decoration: underline;">
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