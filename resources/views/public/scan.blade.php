@extends('layouts.app')
@section('title','Scan QR Code')
@section('content')
<section class="hero">
    <div class="container">
        <h1>{{ $film->title }}</h1>
        <p>Scannez ce film pour votre progression</p>
    </div>
</section>

<section class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">{{ $film->title }}</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($film->vignette)
                            <img src="{{ asset('storage/'.$film->vignette) }}" class="img-fluid rounded" alt="{{ $film->title }}">
                        @endif
                        <p class="mt-3">{{ $film->description }}</p>
                    </div>
                    
                    <div class="d-flex justify-content-around">
                        <a href="{{ route('connexion.express') }}?film_slug={{ $film->slug }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> J'ai déjà un compte
                        </a>
                        <a href="{{ route('inscription') }}?from_qr_scan=1&film_slug={{ $film->slug }}" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Je m'inscris
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection