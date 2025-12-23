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
    <div class="sidebar">
        <h2><i class="fas fa-film"></i> Madiana Admin</h2>
        <a href="{{ route('admin.stats') }}" class="{{ request()->routeIs('admin.stats') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Statistiques
        </a>
        <a href="{{ route('admin.films') }}" class="{{ request()->routeIs('admin.films*') ? 'active' : '' }}">
            <i class="fas fa-video"></i> Films
        </a>
        <a href="{{ route('admin.tirages') }}" class="{{ request()->routeIs('admin.tirages*') ? 'active' : '' }}">
            <i class="fas fa-gift"></i> Tirages au sort
        </a>
        <a href="{{ route('admin.dotations') }}" class="{{ request()->routeIs('admin.dotations*') ? 'active' : '' }}">
            <i class="fas fa-trophy"></i> Dotations
        </a>
    </div>
    <div class="content">
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

    <!-- Modals -->
    @yield('modals')

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
    </script>
    
    <!-- Inclure les scripts poussés -->
    @stack('scripts')
</body>
</html>