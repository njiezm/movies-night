@extends('layouts.app')

@section('title','Mes Films')

@section('content')
<link href="{{ asset('css/madiana-mes-films.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-xl-5 col-12 main_view">
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
                        <!-- Titre et accroche -->
                        <!--div class="row justify-content-center">
                            <div class="col-11 text-center mg-top-10">
                                <h1>Bonjour {{ App\Models\Base\Genesys::Decrypt($participant->firstname) }}</h1>
                                <p class="text-warning lead">Votre progression dans le marathon</p>
                            </div>
                        </div-->
                        <div class="row justify-content-center mb-4">
                        <div class="col-12 text-center">
                            <div class="">
                                <img src="{{ asset('images/madiana/mes-films.png') }}" class="img-fluid" alt="{{ $film->title }}">
                            </div>
                        </div>
                    </div>

                        @if($film)
                        <div class="row justify-content-center mb-4">
                            <div class="col-11 text-center">
                                <div class="film-scanned" style="padding: 15px; border-radius: 10px;background-color: rgba(255, 255, 255, 0.05);border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <img src="{{ asset('storage/'.$film->vignette) }}" class="img-fluid rounded" alt="{{ $film->title }}">
                                    <h2 class="text-white mt-3">{{ $film->title }}</h2>
                                    <!--p class="text-white-50">{{ $film->description }}</!--p-->
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- films vus -->
                        <!--p>{{ $filmsVus->count() }} / {{ $total }} Films </!--p-->
                        @if($filmsVus->count() == 0)
                        <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <div class="">
                                    <img src="{{ asset('images/madiana/0-6-f.png') }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($filmsVus->count() == 1)
                        <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <div class="">
                                    <img src="{{ asset('images/madiana/1-6-f.png') }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($filmsVus->count() == 2)
                             <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <div class="">
                                    <img src="{{ asset('images/madiana/2-6-f.png') }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($filmsVus->count() == 3)
                            <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <div class="">
                                    <img src="{{ asset('images/madiana/3-6-f.png') }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($filmsVus->count() == 4)
                            <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <div class="">
                                    <img src="{{ asset('images/madiana/4-6-f.png') }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($filmsVus->count() == 5)
                          <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <div class="">
                                    <img src="{{ asset('images/madiana/5-6-f.png') }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($filmsVus->count() == 6)
                          <div class="row justify-content-center">
                            <div class="col-12 text-center">
                                <div class="">
                                    <img src="{{ asset('images/madiana/6-6-f.png') }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($filmsVus->count() !=6)
                                <div class="row justify-content-center mt-5 mb-5">
                                    <div class="col-10 col-sm-4 mg-top-5 text-center mg-bottom-40">
                                        <button type="submit" style="background: transparent; border: none;" class="btn-submit-img">
                                            <img src="{{ asset('images/madiana/selection-films.png') }}"
                                                class="img-fluid"
                                                alt="Selection de films">
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="row justify-content-center mt-3">
                                    <div class="col-7 col-sm-3 mg-top-1 text-center mg-bottom-40">
                                            <img src="{{ asset('images/madiana/bonne-chance.png') }}"
                                                class="img-fluid"
                                                alt="Selection de films">
                                    </div>
                                </div>
                                @endif
                        
                        <!-- Barre de progression -->
                        <!--div class="row justify-content-center mb-4">
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
                        </!--div-->

                        <!-- Liste des films vus -->
                        <!--div class="row justify-content-center">
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
                        </!--div-->
                        
                        <!-- Appel à l'action final -->
                        <!--div class="text-center mt-4">
                            <p class="text-white">Bon film et bon courage !</p>
                            <a href="{{ route('rendez.vous') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                <i class="fas fa-home"></i> Retour à l'accueil
                            </a>
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