<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login | {{ config('site.name', 'Streaming Platform') }}</title>
    <link rel="icon" href="{{ asset('/images/static_files/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel - Promotional Image -->
        <div class="promotional-panel">
            <div class="promotional-content">
                <div class="promo-text-top">{{ config('site.name') }}</div>
                <div class="promo-text-bottom">
                    <div class="promo-name">{{ config('site.owner.name') }}</div>
                    <div class="promo-description">
                        {{ config('site.owner.welcome_note') }}
                    </div>
                </div>
            </div>
            <div class="promotional-overlay"></div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="login-form-panel">
            
            <div class="login-content">
                <!-- Logo -->
                <div class="logo-container">
                    
                    <div class="logo-icon">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 4L20 12L28 14L20 16L16 24L12 16L4 14L12 12L16 4Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <span class="logo-text">{{ config('site.company_acronym') }}</span>
                </div>

                <!-- Welcome Message -->
                <div class="welcome-section">
                    <h1 class="welcome-title">Welcome Boss!</h1>
                    <p class="welcome-subtitle">{{ config('site.name') }}, {{ config('site.motto') }}</p>
                </div>

                <!-- Superstar Role Error Message -->
                @if(session('superstar_error'))
                    <div class="alert alert-error" style="background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                        {{ session('superstar_error') }}
                    </div>
                @endif

                <!-- General Error Messages -->
                @if($errors->any())
                    <div class="alert alert-error" style="background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input @error('email') is-invalid @enderror" 
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input @error('password') is-invalid @enderror" 
                            placeholder="Enter your password"
                            required
                        >
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" id="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="/">Back to Home Page</a>
                        
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" class="btn-signin">Sign In</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Immediate redirect if page is loaded from cache and user might be authenticated
        // Force a fresh check with the server
        if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
            // User pressed back button, force reload from server
            window.location.href = window.location.href + (window.location.href.indexOf('?') > -1 ? '&' : '?') + '_t=' + new Date().getTime();
        }

        // Prevent back button from showing cached login page
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was loaded from cache, reload from server with timestamp
                window.location.href = window.location.href.split('?')[0] + '?_t=' + new Date().getTime();
            }
        });

        // Prevent browser from caching this page
        window.addEventListener('beforeunload', function() {
            // Clear any potential cache
        });

        // Check if user is already authenticated (client-side check)
        // This is a fallback - server-side check is primary
        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href);
            window.addEventListener('popstate', function() {
                window.history.pushState(null, null, window.location.href);
                // Force reload with timestamp to bypass cache
                window.location.href = window.location.href.split('?')[0] + '?_t=' + new Date().getTime();
            });
        }
    </script>
</body>
</html>

