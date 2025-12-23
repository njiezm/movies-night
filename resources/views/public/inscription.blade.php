@extends('layouts.app')
@section('title','Inscription Marathon')
@section('content')
<section class="hero">
    <div class="container">
        <h1>Marathon de Films d'Horreur</h1>
        <p>Rejoignez-nous pour une nuit de frissons et de suspense !</p>
    </div>
</section>

<section class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Inscription</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('film_slug'))
                        <div class="alert alert-info">
                            Scannez ce film pour votre progression !
                        </div>
                    @endif
                    
                    <form action="{{ route('inscription.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="source" value="{{ $source ?? '' }}">
                        @if(request('film_slug'))
                            <input type="hidden" name="film_slug" value="{{ request('film_slug') }}">
                            <input type="hidden" name="from_qr_scan" value="1">
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname">Prénom</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Nom</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">Téléphone</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="zipcode">Code postal</label>
                                    <input type="text" class="form-control" id="zipcode" name="zipcode">
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn">S'inscrire</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Déjà inscrit ? 
                            @if(request('film_slug'))
                                <a href="{{ route('connexion.express') }}?film_slug={{ request('film_slug') }}">Connectez-vous</a>
                            @else
                                <a href="{{ route('connexion.express') }}">Connectez-vous</a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection