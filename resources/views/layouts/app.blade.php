<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Marathon Films')</title>
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <a href="{{ url('/') }}" class="logo">Madiana</a>
            <nav>
                <ul>
                    <li><a href="{{ url('/') }}">Accueil</a></li>
                    <li><a href="{{ route('rendez.vous') }}">Événement</a></li>
                    <li><a href="{{ url('/inscription') }}">Inscription</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="container">
            <div>
                <h3>Marathon de Films</h3>
                <p>Participez à notre marathon de films d'horreur et gagnez des prix fantastiques !</p>
            </div>
            <div>
                <h3>Liens utiles</h3>
                <ul>
                    <li><a href="{{ url('/') }}">Accueil</a></li>
                    <li><a href="{{ route('rendez.vous') }}">Événement</a></li>
                    <li><a href="{{ url('/inscription') }}">Inscription</a></li>
                </ul>
            </div>
            <div>
                <h3>Contact</h3>
                <p>Cinéma Madiana<br>
                Martinique<br>
                <a href="mailto:contact@madiana.com">contact@madiana.com</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Scripts communs
        $(document).ready(function() {
            // Animations simples
            $('.card').on('mouseenter', function() {
                $(this).addClass('hover');
            }).on('mouseleave', function() {
                $(this).removeClass('hover');
            });
        });
    </script>
</body>
</html>