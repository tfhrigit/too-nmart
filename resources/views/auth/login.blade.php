<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Login') }} - Nmart-Build</title>

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
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #334155;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .company-header {
            background-color: #1e40af;
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .company-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #60a5fa;
            border-radius: 2px;
        }

        .company-logo {
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .company-name {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .company-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 8px;
            font-weight: 400;
            color: #dbeafe;
        }

        .login-content {
            padding: 40px;
        }

        .form-title {
            text-align: center;
            color: #1e40af;
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-label {
            display: block;
            color: #475569;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .input-field {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
            background: white;
            color: #334155;
        }

        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-error {
            color: #dc2626;
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
            accent-color: #3b82f6;
            border-radius: 4px;
        }

        .remember-me span {
            color: #64748b;
            font-size: 0.875rem;
        }

        .forgot-password {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
            font-weight: 500;
        }

        .forgot-password:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 14px;
            background-color: #1e40af;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .login-button:hover {
            background-color: #1d4ed8;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
        }

        .login-button:active {
            transform: translateY(1px);
        }

        .session-status {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.875rem;
            display: none;
        }

        .session-status.success {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .session-status.error {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #94a3b8;
            font-size: 0.875rem;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 480px) {
            .login-container {
                border-radius: 10px;
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
        
        .password-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            font-size: 0.875rem;
        }
        
        .password-toggle:hover {
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-logo">
                <img src="{{ asset('img/logo3.png') }}"
                     alt="logo"
                     style="height: 60px; width: auto;">
            </div>
           
            <h1 class="company-name">Nmart-Build</h1>
            <div class="company-subtitle">SISTEM MANAJEMEN BANGUNAN
            </div>
        </div>

        <!-- Login Form -->
        <div class="login-content">
            <!-- Session Status -->
            <div class="session-status success" style="display: none;">
                Status message will appear here
            </div>

            <h2 class="form-title">Login ke Akun Anda</h2>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
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
                    <div class="password-container">
                        <input id="password" class="input-field" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                        <button type="button" class="password-toggle" id="togglePassword">Tampilkan</button>
                    </div>
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
                &copy; {{ date('Y') }} Nmart-Build. Hak Cipta Dilindungi.
            </div>
        </div>
    </div>

    <!-- JavaScript for enhanced functionality -->
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
            
            // Password toggle functionality
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.textContent = type === 'password' ? 'Tampilkan' : 'Sembunyikan';
                });
            }
            
            // Form validation feedback
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInputField = document.getElementById('password');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    let valid = true;
                    
                    // Reset error states
                    document.querySelectorAll('.input-error').forEach(el => {
                        el.style.display = 'none';
                    });
                    
                    // Email validation
                    if (!emailInput.value.trim()) {
                        showError(emailInput, 'Email diperlukan');
                        valid = false;
                    } else if (!isValidEmail(emailInput.value)) {
                        showError(emailInput, 'Format email tidak valid');
                        valid = false;
                    }
                    
                    // Password validation
                    if (!passwordInputField.value.trim()) {
                        showError(passwordInputField, 'Password diperlukan');
                        valid = false;
                    }
                    
                    if (!valid) {
                        e.preventDefault();
                    }
                });
            }
            
            // Real-time validation
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                    if (this.value.trim() && !isValidEmail(this.value)) {
                        showError(this, 'Format email tidak valid');
                    } else {
                        hideError(this);
                    }
                });
            }
            
            if (passwordInputField) {
                passwordInputField.addEventListener('blur', function() {
                    if (this.value.trim() && this.value.length < 6) {
                        showError(this, 'Password minimal 6 karakter');
                    } else if (this.value.trim()) {
                        hideError(this);
                    }
                });
            }
            
            // Helper functions
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            function showError(inputElement, message) {
                let errorElement = inputElement.parentElement.querySelector('.input-error');
                if (!errorElement) {
                    errorElement = document.createElement('div');
                    errorElement.className = 'input-error';
                    inputElement.parentElement.appendChild(errorElement);
                }
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                inputElement.style.borderColor = '#dc2626';
            }
            
            function hideError(inputElement) {
                const errorElement = inputElement.parentElement.querySelector('.input-error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
                inputElement.style.borderColor = '#cbd5e1';
            }
        });
    </script>
</body>
</html>