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
                    <img src="{{ asset('images/madiana/header.png') }}" class="img-fluid" alt="Marathon de Films d'Horreur">
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="p-4">
                        <!-- Titre et accroche -->
                        <!--div class="row justify-content-center">
                            <div class="col-11 text-center mg-top-10">
                                <h2 class="text-white mb-3">Marathon de Films d'Horreur</h2>
                                <p class="text-white">Rejoignez-nous pour une nuit de frissons et de suspense !</p>
                            </div>
                        </!--div-->

                        <!-- Image de dotation -->
                        <div class="row justify-content-center">
                            <div class="col-12 text-center mg-top-20 mg-bottom-20">
                                <img src="{{ asset('images/madiana/dotation.png') }}" class="img-fluid rounded" alt="Dotation">
                            </div>
                        </div>

                        <!-- Formulaire -->
                        <div style="" class="row justify-content-center">
                            <!--div class="row justify-content-center">
                                <div class="col-12 text-center mg-top-10 mg-bottom-20">
                                    <h3 class="text-white">Inscrivez-vous maintenant</h3>
                                </div>
                            </!--div-->

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
                                        <input type="text" style="background: rgba(255, 255, 255, 0.20); color:white;" class="form-control text-center mg-top-5 rounded-pill input-white-big" name="firstname" placeholder="Prénom" required/>
                                    </div>
                                </div>

                                <div class="row p-2">
                                    <div class="col mg-top-5">
                                        <input type="text" style="background: rgba(255, 255, 255, 0.20); color:white;" class="form-control text-center mg-top-5 rounded-pill input-white-big" name="lastname" placeholder="Nom" required/>
                                    </div>
                                </div>

                                <div class="row p-2">
                                    <div class="col mg-top-5">
                                        <input type="email" style="background: rgba(255, 255, 255, 0.20); color:white;" class="form-control text-center mg-top-5 rounded-pill input-white-big" name="email" placeholder="Email"/>
                                    </div>
                                </div>

                                <div class="row p-2">
                                    <div class="col mg-top-5">
                                        <input type="tel" style="background: rgba(255, 255, 255, 0.20); color:white;" class="form-control text-center mg-top-5 rounded-pill input-white-big" name="telephone" placeholder="Numéro de téléphone" required/>
                                    </div>
                                </div>

                                <!--div class="row p-2">
                                    <div class="col mg-top-5">
                                        <input type="text" class="form-control text-center mg-top-5 rounded-pill" name="zipcode" placeholder="Code postal"/>
                                    </div>
                                </!--div-->

                            <div class="row p-2">
    <div class="col mg-top-5">
        <label style="font-size: 1.8rem; " class="text-white">Souhaitez-vous être recontacté ?</label>
        <select
            class="form-select text-center rounded-pill input-white-big"
            name="optin"
            id="optinSelect"
            required
        >
            <!-- option neutre par défaut -->
            <option value="" selected disabled hidden>
                — Choisissez une réponse —
            </option>

            <option style="background: black" value="0">Non</option>
            <option style="background: black" value="1">Oui</option>
        </select>
    </div>
</div>


                                <div class="row p-2 justify-content-center blockOptincanal" style="display: none;">
                                    <div class="col-12 mg-top-5">
                                        <label style="font-size: 1.8rem; " class="text-white">Comment souhaitez-vous être contacté ?</label>
                                        <select style="background: rgba(255, 255, 255, 0.20);" class="form-select text-center rounded-pill input-white-big" name="contact_method" id="contactMethod">
                                            <option value="">Sélectionnez une option</option>
                                            <option style="background: black" value="1">SMS</option>
                                            <option style="background: black" value="2">Email</option>
                                            <option style="background: black" value="3">Email & SMS</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-4">
                                    <div class="col-8 col-sm-4 mg-top-5 text-center mg-bottom-40">
                                <button type="submit" style="background: transparent; border: none;" class="btn-submit-img">
                                    <img src="{{ asset('images/madiana/valider_btn.png') }}"
                                        class="img-fluid"
                                        alt="S'inscrire">
                                </button>
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
                    <img src="{{ asset('images/madiana/footer.png') }}" class="img-fluid" alt="Cinéma Madiana">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    const $form = $('form');
    const $optin = $('#optinSelect');
    const $contactMethod = $('#contactMethod');
    const $blockOptin = $('.blockOptincanal');

    // Affichage / masquage du choix du canal
    $optin.on('change', function () {
        if ($(this).val() === '1') {
            $blockOptin.slideDown(200);
            $contactMethod.prop('required', true);
        } else {
            $blockOptin.slideUp(200);
            $contactMethod.prop('required', false).val('');
        }
    });

    // Validation à la soumission
    $form.on('submit', function (e) {

        // Opt-in non sélectionné
        if (!$optin.val()) {
            e.preventDefault();
            alert('Veuillez choisir si vous souhaitez être recontacté.');
            $optin.focus();
            return false;
        }

        // Opt-in = oui mais aucun canal choisi
        if ($optin.val() === '1' && !$contactMethod.val()) {
            e.preventDefault();
            alert('Veuillez choisir un moyen de contact.');
            $contactMethod.focus();
            return false;
        }

    });

});
</script>

@endsection