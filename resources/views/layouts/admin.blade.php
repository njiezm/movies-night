<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Admin Marathon Films')</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>
    @if(!session('admin_authenticated'))
        <!-- Page de connexion avec le design Madiana amélioré -->
        <div class="login-wrapper">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <div class="logo-container">
                            <i class="fas fa-film"></i>
                        </div>
                        <h2>Madiana Admin</h2>
                        <p>Entrez votre code d'accès pour continuer</p>
                    </div>

                    <form action="{{ route('admin.login') }}" method="POST" class="login-form">
                        @csrf
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" name="access_code" placeholder="Code à 6 chiffres" maxlength="6" pattern="[0-9]{6}" required>
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <span>Se connecter</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <!-- Interface d'administration -->
        <div class="admin-wrapper">
            <!-- Header pour mobile -->
            <header class="admin-header-mobile">
                <div class="header-left">
                    <button class="menu-toggle" id="mobileMenuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="admin-title-mobile">Madiana Admin</h1>
                </div>
                <a href="{{ route('admin.logout') }}" class="logout-mobile">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </header>

            <!-- Navigation latérale pour mobile -->
            <nav class="admin-nav-mobile" id="mobileNav">
                <div class="nav-header">
                    <h3>Menu</h3>
                    <button class="close-nav" id="closeMobileNav">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <ul class="nav-list">
                    <li>
                        <a href="{{ route('admin.stats') }}" class="{{ request()->routeIs('admin.stats') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.films') }}" class="{{ request()->routeIs('admin.films*') ? 'active' : '' }}">
                            <i class="fas fa-video"></i> Films
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tirages') }}" class="{{ request()->routeIs('admin.tirages*') ? 'active' : '' }}">
                            <i class="fas fa-gift"></i> Tirages
                        </a>
                    </li>
                    @if(session('show_dotations'))
                        <li>
                            <a href="{{ route('admin.dotations') }}" class="{{ request()->routeIs('admin.dotations*') ? 'active' : '' }}">
                                <i class="fas fa-trophy"></i> Dotations
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('admin.logout') }}" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Contenu principal -->
            <main class="admin-main">
                <div class="admin-container">
                    <!-- Navigation desktop -->
                    <nav class="admin-nav-desktop">
                        <div class="nav-brand">
                            <i class="fas fa-film"></i>
                            <span>Madiana Admin</span>
                        </div>
                        <ul class="nav-list">
                            <li>
                                <a href="{{ route('admin.stats') }}" class="{{ request()->routeIs('admin.stats') ? 'active' : '' }}">
                                    <i class="fas fa-chart-bar"></i> Statistiques
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.films') }}" class="{{ request()->routeIs('admin.films*') ? 'active' : '' }}">
                                    <i class="fas fa-video"></i> Films
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.tirages') }}" class="{{ request()->routeIs('admin.tirages*') ? 'active' : '' }}">
                                    <i class="fas fa-gift"></i> Tirages
                                </a>
                            </li>
                            @if(session('show_dotations'))
                                <li>
                                    <a href="{{ route('admin.dotations') }}" class="{{ request()->routeIs('admin.dotations*') ? 'active' : '' }}">
                                        <i class="fas fa-trophy"></i> Dotations
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                                        <i class="fas fa-cog"></i> Réglages
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('admin.logout') }}" class="logout-link">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <!-- Contenu de la page -->
                    <section class="admin-content">
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @yield('content')
                    </section>
                </div>
            </main>
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
        // Menu mobile
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileNav = document.getElementById('mobileNav');
            const closeMobileNav = document.getElementById('closeMobileNav');
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    mobileNav.classList.add('active');
                });
            }
            
            if (closeMobileNav) {
                closeMobileNav.addEventListener('click', function() {
                    mobileNav.classList.remove('active');
                });
            }
            
            // Fermer le menu en cliquant à l'extérieur
            document.addEventListener('click', function(event) {
                if (!mobileNav.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                    mobileNav.classList.remove('active');
                }
            });
        });

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
    
</body>
</html>