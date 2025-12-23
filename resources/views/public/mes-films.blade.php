@extends('layouts.app')
@section('title','Mes Films')
@section('content')
<section class="hero">
    <div class="container">
        <h1>Bonjour {{ $participant->firstname }}</h1>
        <p>Votre progression dans le marathon</p>
    </div>
</section>

<section class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Votre progression</h3>
                </div>
                <div class="card-body">
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" style="width: {{ ($total > 0) ? ($filmsVus->count() / $total) * 100 : 0 }}%;" aria-valuenow="{{ $filmsVus->count() }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                            {{ $filmsVus->count() }} / {{ $total }}
                        </div>
                    </div>
                    
                    <h4>Films que vous avez vus:</h4>
                    <div class="film-grid">
                        @foreach($filmsVus as $film)
                            <div class="film-card">
                                @if($film->vignette)
                                    <img src="{{ asset('storage/'.$film->vignette) }}" alt="{{ $film->title }}">
                                @else
                                    <img src="https://via.placeholder.com/300x200?text={{ urlencode($film->title) }}" alt="{{ $film->title }}">
                                @endif
                                <div class="film-card-content">
                                    <h5 class="film-card-title">{{ $film->title }}</h5>
                                    <p class="film-card-description">{{ $film->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center mt-4">
                        <p class="lead">Bon film !</p>
                        <a href="{{ route('rendez.vous') }}" class="btn btn-outline">Retour à l'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection