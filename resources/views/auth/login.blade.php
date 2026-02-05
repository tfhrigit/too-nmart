<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Login') }} - PT PUTRA JAYA SAMPANGAN</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Figtree', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #fdf2f8 0%, #f472b6 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .company-header {
            background: linear-gradient(to right, #ec4899, #db2777);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .company-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 10%;
            width: 80%;
            height: 2px;
            background: rgba(255, 255, 255, 0.3);
        }

        .company-logo {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        .company-name {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .company-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
            letter-spacing: 2px;
        }

        .login-content {
            padding: 40px;
        }

        .form-title {
            text-align: center;
            color: #be185d;
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-label {
            display: block;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .input-field {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f9fafb;
        }

        .input-field:focus {
            outline: none;
             border-color: #ec4899;
    box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.25);
            background: white;
        }

        .input-error {
            color: #be185d;
            font-size: 0.875rem;
            margin-top: 6px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #ec4899;
        }

        .remember-me span {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .forgot-password {
            color: #db2777;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: #be185d;
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, #ec4899, #db2777);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .login-button:hover {
            transform: translateY(-2px);
             box-shadow: 0 10px 20px rgba(236, 72, 153, 0.4);
        }

        .session-status {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.875rem;
        }

        .session-status.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 0.875rem;
        }

        @media (max-width: 480px) {
            .login-container {
                border-radius: 15px;
            }
            
            .company-header {
                padding: 25px 20px;
            }
            
            .company-name {
                font-size: 1.4rem;
            }
            
            .login-content {
                padding: 30px 20px;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }

        .icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-logo">
    <img src="{{ asset('img/logo3.png') }}"
         alt="Logo PT Putra Jaya Sampangan"
         style="height: 60px; width: auto;">
</div>
           
            <h1 class="company-name">PT PUTRA JAYA SAMPANGAN</h1>
            <div class="company-subtitle">LOGIN SYSTEM</div>
        </div>

        <!-- Login Form -->
        <div class="login-content">
            <!-- Session Status -->
            <div class="session-status success" style="display: none;">
                Status message will appear here
            </div>

            <h2 class="form-title">Login ke Akun Anda</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="input-group">
                    <label class="input-label" for="email">{{ __('Email') }}</label>
                    <input id="email" class="input-field" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@perusahaan.com">
                    @if ($errors->has('email'))
                        <div class="input-error">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label class="input-label" for="password">{{ __('Password') }}</label>
                    <input id="password" class="input-field" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    @if ($errors->has('password'))
                        <div class="input-error">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="remember-forgot">
                    <label class="remember-me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot-password" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="icon">
                        <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 006 5.25v13.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V15a.75.75 0 011.5 0v3.75a3 3 0 01-3 3h-6a3 3 0 01-3-3V5.25a3 3 0 013-3h6a3 3 0 013 3V9A.75.75 0 0115 9V5.25a1.5 1.5 0 00-1.5-1.5h-6zm10.72 4.72a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06l-3 3a.75.75 0 11-1.06-1.06l1.72-1.72H9a.75.75 0 010-1.5h10.94l-1.72-1.72a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Log in') }}
                </button>
            </form>

            <div class="footer-text">
                &copy; {{ date('Y') }} PT PUTRA JAYA SAMPANGAN. All rights reserved.
            </div>
        </div>
    </div>

    <!-- JavaScript for Session Status -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's a session status message
            const sessionStatus = "{{ session('status') }}";
            const errorMessages = {
                email: "{{ $errors->first('email') }}",
                password: "{{ $errors->first('password') }}"
            };
            
            // Show session status if exists
            if (sessionStatus) {
                const statusElement = document.querySelector('.session-status');
                statusElement.textContent = sessionStatus;
                statusElement.style.display = 'block';
                
                // Hide after 5 seconds
                setTimeout(() => {
                    statusElement.style.display = 'none';
                }, 5000);
            }
            
            // Add focus effects to inputs
            const inputs = document.querySelectorAll('.input-field');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
        });
    </script>
</body>
</html>