@extends('layouts.app')
@section('title','Rendez-vous')
@section('content')
<section class="hero">
    <div class="container">
        <h1>Marathon de Films d'Horreur</h1>
        <p>Une nuit de frissons vous attend !</p>
    </div>
</section>

<section class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Rendez-vous au Cinéma Madiana</h3>
                </div>
                <div class="card-body text-center">
                    <h4>Préparez-vous pour la course au film d'horreur</h4>
                    <p class="lead">Scannez les QR codes dans le cinéma pour participer à la course et découvrez si vous survivrez à tous les films !</p>
                    
                    <img src="{{ asset('images/horror-movie.jpg') }}" class="img-fluid rounded mt-3 mb-4" alt="Horror Movie">
                    
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <i class="fas fa-calendar-alt fa-2x text-primary mb-3"></i>
                                    <h5>Date</h5>
                                    <p>31 Octobre 2023</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                                    <h5>Heure</h5>
                                    <p>20:00 - 02:00</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
                                    <h5>Lieu</h5>
                                    <p>Cinéma Madiana</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <p>Les QR codes seront disponibles uniquement dans le cinéma lors de l'événement.</p>
                        <p>Inscrivez-vous dès maintenant pour participer à la course !</p>
                        <a href="{{ url('/inscription') }}" class="btn btn-lg">S'inscrire maintenant</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection