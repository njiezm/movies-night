@extends('layouts.app')
@section('title','Connexion Express')
@section('content')
<section class="hero">
    <div class="container">
        <h1>Connexion Express</h1>
        <p>Accédez rapidement à votre progression</p>
    </div>
</section>

<section class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Connexion Express</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    
                    <form action="{{ route('connexion.express.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="film_slug" value="{{ request('film_slug') }}">
                        
                        <div class="form-group">
                            <label for="telephone">Numéro de téléphone</label>
                            <input type="text" class="form-control" id="telephone" name="telephone" required>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn">Connexion</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Pas encore inscrit ? 
                            @if(request('film_slug'))
                                <a href="{{ route('inscription', ['source' => 'qr_scan']) }}?film_slug={{ request('film_slug') }}">Inscrivez-vous</a>
                            @else
                                <a href="{{ route('inscription') }}">Inscrivez-vous</a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection