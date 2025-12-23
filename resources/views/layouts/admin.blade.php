<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Admin Marathon Films')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    @if(!session('admin_authenticated'))
        <!-- Page de connexion avec le design Madiana -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-xl-5 col-12 main_view">
                    <div class="row justify-content-center">
                        <div class="col">
                            <img src="{{ asset('images/madiana/admin-header.jpg') }}" class="shadow img-fluid header-img" alt="Admin Madiana">
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-10 card_red">
                            <div class="p-4">
                                <div class="row justify-content-center">
                                    <div class="col-11 text-center">
                                        <h2 class="text-white mb-3">Connexion Admin</h2>
                                        <p class="text-white">Entrez votre code d'accès pour continuer</p>
                                    </div>
                                </div>

                                <form action="{{ route('admin.login') }}" method="POST">
                                    @csrf
                                    <div class="row p-2">
                                        <div class="col">
                                            <input type="text" class="form-control text-center rounded-pill" name="access_code" placeholder="Code à 6 chiffres" maxlength="6" pattern="[0-9]{6}" required>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center mt-4">
                                        <div class="col-8 col-sm-4">
                                            <button type="submit" class="btn btn-danger btn-lg rounded-pill w-100">Se connecter</button>
                                        </div>
                                    </div>
                                </form>

                                @if(session('error'))
                                    <div class="alert alert-danger mt-3 text-center">
                                        {{ session('error') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 text-center">
                            <img src="{{ asset('images/madiana/admin-footer.jpg') }}" class="img-fluid footer-img" alt="Cinéma Madiana">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Interface d'administration -->
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10 main_view">
                    <!-- Header -->
                    <div class="row justify-content-center">
                        <div class="col">
                            <img src="{{ asset('images/madiana/admin-header.jpg') }}" class="shadow img-fluid header-img" alt="Admin Madiana">
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="row justify-content-center">
                        <div class="col-10 nav-admin">
                            <a href="{{ route('admin.stats') }}" class="{{ request()->routeIs('admin.stats') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i> Statistiques
                            </a>
                            <a href="{{ route('admin.films') }}" class="{{ request()->routeIs('admin.films*') ? 'active' : '' }}">
                                <i class="fas fa-video"></i> Films
                            </a>
                            <a href="{{ route('admin.tirages') }}" class="{{ request()->routeIs('admin.tirages*') ? 'active' : '' }}">
                                <i class="fas fa-gift"></i> Tirages
                            </a>
                            @if(session('show_dotations'))
                                <a href="{{ route('admin.dotations') }}" class="{{ request()->routeIs('admin.dotations*') ? 'active' : '' }}">
                                    <i class="fas fa-trophy"></i> Dotations
                                </a>
                            @endif
                            <a href="{{ route('admin.logout') }}" class="logout-link">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                        </div>
                    </div>

                    <!-- Contenu principal -->
                    <div class="row justify-content-center">
                        <div class="col-10 card_red">
                            <div class="p-4">
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                
                                @yield('content')
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="row justify-content-center">
                        <div class="col-12 text-center">
                            <img src="{{ asset('images/madiana/admin-footer.jpg') }}" class="img-fluid footer-img" alt="Cinéma Madiana">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal pour afficher le QR code en grand -->
        <div id="qrModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>QR Code</h4>
                    <span class="close">&times;</span>
                </div>
                <div class="modal-body text-center">
                    <img id="qrModalImage" src="" alt="QR Code">
                    <div class="qr-link mt-3">
                        <label>Lien du QR code:</label>
                        <div class="input-group">
                            <input type="text" id="qrModalLink" class="form-control" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-danger" onclick="copyQrLink()">
                                    <i class="fas fa-copy"></i> Copier
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Fermer les modals
        $(document).on('click', '.close', function() {
            $(this).closest('.modal').hide();
        });

        // Fermer les modals en cliquant à l'extérieur
        $(document).on('click', '.modal', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });

        // Afficher le QR code en grand
        function showQrModal(imageSrc, link) {
            $('#qrModalImage').attr('src', imageSrc);
            $('#qrModalLink').val(link);
            $('#qrModal').show();
        }

        // Copier le lien du QR code
        function copyQrLink() {
            var copyText = document.getElementById("qrModalLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            
            var button = $(event.target).closest('button');
            var originalText = button.html();
            button.html('<i class="fas fa-check"></i> Copié!');
            setTimeout(function() {
                button.html(originalText);
            }, 2000);
        }
    </script>
    
    @stack('scripts')
</body>
</html>