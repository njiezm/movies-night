<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Madiana Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Styles spécifiques à la page de connexion */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image:
                url("/images/madiana/pattern.jpg"),
                radial-gradient(
                    ellipse at center,
                    #222 0%,
                    #8B0000 50%,
                    #330000 100%
                );
            background-repeat: repeat, no-repeat;
            background-size: contain, cover;
            background-position: top left, center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .login-card {
            background-color: rgba(0, 0, 0, 0.85);
            border: 1px solid rgba(229, 9, 20, 0.5);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 2;
            animation: slideIn 0.5s ease-out;
            backdrop-filter: blur(5px);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: var(--text-color);
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-header h2 i {
            color: var(--primary-color);
            font-size: 32px;
        }

        .login-header p {
            color: #aaa;
            font-size: 16px;
            margin: 0;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: var(--text-color);
            font-size: 18px;
            text-align: center;
            letter-spacing: 5px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(229, 9, 20, 0.25);
            outline: none;
        }

        .input-group {
            position: relative;
        }

        .input-group-append {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
            transition: color 0.3s ease;
        }

        .input-group-append:hover {
            color: var(--primary-color);
        }

        .btn-primary {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary:hover {
            background-color: #c50812;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(229, 9, 20, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.8);
            border: none;
            color: white;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.8);
            border: none;
            color: white;
        }

        .form-footer {
            margin-top: 25px;
            text-align: center;
            color: #aaa;
            font-size: 14px;
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            border-radius: 15px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .access-code-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 25px;
        }

        .access-code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .access-code-input:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(229, 9, 20, 0.25);
            outline: none;
        }

        .access-code-input.filled {
            background-color: rgba(229, 9, 20, 0.2);
            border-color: var(--primary-color);
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #aaa;
        }

        .remember-me input {
            width: auto;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #fff;
            text-decoration: underline;
        }

        .back-to-site {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #aaa;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 5;
            transition: all 0.3s ease;
        }

        .back-to-site:hover {
            color: var(--text-color);
            transform: translateX(-5px);
        }

        .security-info {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            font-size: 13px;
            color: #aaa;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .security-info i {
            color: var(--primary-color);
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }

            .access-code-input {
                width: 40px;
                height: 50px;
                font-size: 20px;
            }

            .login-header h2 {
                font-size: 24px;
            }

            .login-header p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="back-to-site">
        <i class="fas fa-arrow-left"></i> Retour au site
    </a>

    <div class="login-container">
        <div class="login-card">
            <div class="loading-overlay">
                <div class="spinner"></div>
            </div>

            <div class="login-header">
                <h2><i class="fas fa-film"></i> Madiana Admin</h2>
                <p>Entrez votre code d'accès</p>
            </div>

            <form id="loginForm" action="{{ route('admin.login') }}" method="POST">
                @csrf
                
                <div class="access-code-inputs">
                    <input type="text" class="access-code-input" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="access-code-input" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="access-code-input" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="access-code-input" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="access-code-input" maxlength="1" pattern="[0-9]" required>
                    <input type="text" class="access-code-input" maxlength="1" pattern="[0-9]" required>
                    <input type="hidden" name="access_code" id="access_code">
                </div>

                <div class="login-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="#" class="forgot-password">Code oublié?</a>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>

                @if(session('error'))
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
            </form>

            <div class="security-info">
                <i class="fas fa-shield-alt"></i>
                <span>Votre connexion est sécurisée et cryptée. Le code d'accès est personnel et ne doit pas être partagé.</span>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Gestion des champs de saisie du code d'accès
            const codeInputs = $('.access-code-input');
            const hiddenInput = $('#access_code');
            
            codeInputs.on('input', function() {
                const value = $(this).val();
                
                // Vérifier si la valeur est un chiffre
                if (!/^\d*$/.test(value)) {
                    $(this).val('');
                    return;
                }
                
                // Mettre à jour le champ caché
                updateHiddenInput();
                
                // Passer au champ suivant
                if (value.length === 1) {
                    $(this).addClass('filled');
                    const nextInput = $(this).next('.access-code-input');
                    if (nextInput.length) {
                        nextInput.focus();
                    }
                } else {
                    $(this).removeClass('filled');
                }
            });
            
            codeInputs.on('keydown', function(e) {
                // Gérer la touche Retour arrière
                if (e.key === 'Backspace' && $(this).val() === '') {
                    const prevInput = $(this).prev('.access-code-input');
                    if (prevInput.length) {
                        prevInput.focus();
                    }
                }
            });
            
            // Mettre à jour le champ caché avec le code complet
            function updateHiddenInput() {
                let code = '';
                codeInputs.each(function() {
                    code += $(this).val();
                });
                hiddenInput.val(code);
            }
            
            // Soumission du formulaire
            $('#loginForm').on('submit', function(e) {
                // Vérifier si tous les champs sont remplis
                let allFilled = true;
                codeInputs.each(function() {
                    if ($(this).val() === '') {
                        allFilled = false;
                        return false;
                    }
                });
                
                if (!allFilled) {
                    e.preventDefault();
                    
                    // Afficher une alerte si le code est incomplet
                    if (!$('.alert-danger').length) {
                        $(this).append('<div class="alert alert-danger mt-3"><i class="fas fa-exclamation-circle"></i> Veuillez entrer un code à 6 chiffres complet.</div>');
                    }
                    
                    // Mettre en évidence le premier champ vide
                    codeInputs.each(function() {
                        if ($(this).val() === '') {
                            $(this).focus();
                            return false;
                        }
                    });
                    
                    return false;
                }
                
                // Afficher l'indicateur de chargement
                $('.loading-overlay').addClass('active');
            });
            
            // Gestion du lien "Code oublié?"
            $('.forgot-password').on('click', function(e) {
                e.preventDefault();
                
                // Remplacer le formulaire par un message
                $('.login-header p').text('Contactez l\'administrateur pour réinitialiser votre code d\'accès');
                $('#loginForm').hide();
                
                // Afficher un bouton pour revenir au formulaire
                if (!$('.back-to-login').length) {
                    $('.login-card').append('<button class="btn btn-primary back-to-login mt-3"><i class="fas fa-arrow-left"></i> Retour à la connexion</button>');
                }
            });
            
            // Gestion du bouton "Retour à la connexion"
            $(document).on('click', '.back-to-login', function() {
                $('.login-header p').text('Entrez votre code d\'accès');
                $('#loginForm').show();
                $(this).remove();
            });
            
            // Animation des champs au focus
            codeInputs.on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });
        });
    </script>
</body>
</html>