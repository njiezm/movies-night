@extends('layouts.app')

@section('title','Inscription Marathon')

@section('content')
<link href="{{ asset('css/madiana-inscription.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5 col-12 main_view">
            <!-- Header -->
            <div class="row justify-content-center">
                <div class="col">
                    <img src="{{ asset('images/madiana/header.jpg') }}" class="shadow img-fluid header-img" alt="Marathon de Films d'Horreur">
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row justify-content-center">
                <div class="col-10 card_red">
                    <div class="p-4">
                        <!-- Titre et accroche -->
                        <div class="row justify-content-center">
                            <div class="col-11 text-center mg-top-10">
                                <h2 class="text-white mb-3">Marathon de Films d'Horreur</h2>
                                <p class="text-white">Rejoignez-nous pour une nuit de frissons et de suspense !</p>
                            </div>
                        </div>

                        <!-- Image de dotation -->
                        <div class="row justify-content-center">
                            <div class="col-12 text-center mg-top-20 mg-bottom-20">
                                <img src="{{ asset('images/madiana/dotation.jpg') }}" class="img-fluid rounded" alt="Dotation">
                            </div>
                        </div>

                        <!-- Formulaire -->
                        <div style="border-radius: 50px; background: rgba(0, 0, 0, 0.7); padding:40px;" class="row justify-content-center">
                            <div class="row justify-content-center">
                                <div class="col-12 text-center mg-top-10 mg-bottom-20">
                                    <h3 class="text-white">Inscrivez-vous maintenant</h3>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('inscription.store') }}">
                                @csrf
                                <input type="hidden" name="source" value="{{ $source ?? 'web' }}">
                                @if(request('film_slug'))
                                    <input type="hidden" name="film_slug" value="{{ request('film_slug') }}">
                                    <input type="hidden" name="from_qr_scan" value="1">
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger mb-3">
                                        @foreach($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger mb-3">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                @if(session('film_slug'))
                                    <div class="alert alert-info mb-3">
                                        Scannez ce film pour votre progression !
                                    </div>
                                @endif

                                <div class="row p-2 mg-top-10">
                                    <div class="col mg-top-5">
                                        <input type="text" class="form-control text-center mg-top-5 rounded-pill" name="firstname" placeholder="Prénom" required/>
                                    </div>
                                </div>

                                <div class="row p-2">
                                    <div class="col mg-top-5">
                                        <input type="text" class="form-control text-center mg-top-5 rounded-pill" name="lastname" placeholder="Nom" required/>
                                    </div>
                                </div>

                                <div class="row p-2">
                                    <div class="col mg-top-5">
                                        <input type="email" class="form-control text-center mg-top-5 rounded-pill" name="email" placeholder="Email"/>
                                    </div>
                                </div>

                                <div class="row p-2">
                                    <div class="col mg-top-5">
                                        <input type="tel" class="form-control text-center mg-top-5 rounded-pill" name="telephone" placeholder="Numéro de téléphone" required/>
                                    </div>
                                </div>

                                <!--div class="row p-2">
                                    <div class="col mg-top-5">
                                        <input type="text" class="form-control text-center mg-top-5 rounded-pill" name="zipcode" placeholder="Code postal"/>
                                    </div>
                                </!--div-->

                                <div class="row p-2">
                                    <div class="col mg-top-5">
                                        <label class="text-white">Souhaitez-vous être recontacté ?</label>
                                        <select class="form-select text-center rounded-pill" name="optin" id="optinSelect">
                                            <option value="0">Non</option>
                                            <option value="1">Oui</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row p-2 justify-content-center blockOptincanal" style="display: none;">
                                    <div class="col-12 mg-top-5">
                                        <label class="text-white">Comment souhaitez-vous être contacté ?</label>
                                        <select class="form-select text-center rounded-pill" name="contact_method" id="contactMethod">
                                            <option value="">Sélectionnez une option</option>
                                            <option value="1">SMS</option>
                                            <option value="2">Email</option>
                                            <option value="3">Email & SMS</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-4">
                                    <div class="col-8 col-sm-4 mg-top-5 text-center mg-bottom-40">
                                        <button type="submit" class="btn btn-danger btn-lg rounded-pill px-4">S'inscrire</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <!--p class="text-white">Déjà inscrit ? 
                                @if(request('film_slug'))
                                    <a href="{{ route('connexion.express') }}?film_slug={{ request('film_slug') }}" class="text-warning">Connectez-vous</a>
                                @else
                                    <a href="{{ route('connexion.express') }}" class="text-warning">Connectez-vous</a>
                                @endif
                            </p-->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <img src="{{ asset('images/madiana/footer.jpg') }}" class="img-fluid footer-img" alt="Cinéma Madiana">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const optinSelect = document.getElementById('optinSelect');
    const contactMethodDiv = document.querySelector('.blockOptincanal');
    
    optinSelect.addEventListener('change', function() {
        if (this.value === '1') {
            contactMethodDiv.style.display = 'block';
        } else {
            contactMethodDiv.style.display = 'none';
        }
    });
});
</script>
@endsection