@extends('layouts.app')
@section('title','Accueil')
@section('content')
<section class="hero">
    <div class="container">
        <h1>Marathon de Films d'Horreur</h1>
        <p>Une nuit de frissons vous attend au Cinéma Madiana</p>
        <a href="{{ route('inscription') }}" class="btn btn-lg">Participer</a>
    </div>
</section>

<section class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-film fa-3x text-primary mb-3"></i>
                    <h4>Films d'horreur</h4>
                    <p>Les meilleurs films d'horreur pour une nuit de frissons inoubliable</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-trophy fa-3x text-primary mb-3"></i>
                    <h4>Prix à gagner</h4>
                    <p>Participez et gagnez des prix fantastiques</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h4>Communauté</h4>
                    <p>Rejoignez une communauté passionnée de cinéma</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">Comment ça marche ?</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h4>Inscrivez-vous</h4>
                        <p>Créez votre compte pour participer au marathon</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="step">
                        <div class="step-number">2</div>
                        <h4>Scannez les QR codes</h4>
                        <p>Scannez les QR codes des films que vous avez vus (disponibles uniquement au cinéma)</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="step">
                        <div class="step-number">3</div>
                        <h4>Gagnez des prix</h4>
                        <p>Plus vous voyez de films, plus vous avez de chances de gagner</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.step {
    position: relative;
    padding: 2rem 1rem;
}

.step-number {
    width: 50px;
    height: 50px;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 auto 1rem;
}
</style>
@endsection