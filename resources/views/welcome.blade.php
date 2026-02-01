<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ config('site.name') }} - {{ config('site.motto') }}. {{ config('site.tagline') }}">
    <meta name="keywords" content="streaming, live streaming, content creators, video platform, monetization">
    
    <title>{{ config('site.name') }} - {{ config('site.tagline') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/static_files/logo.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
    <style>
        .hero-section {
            background-image: linear-gradient(135deg, rgba(26, 26, 26, 0.3) 0%, rgba(45, 45, 45, 0.3) 100%), url('{{ asset('images/static_files/loginimage.png') }}') !important;
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar-custom" id="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="/" class="navbar-brand-custom">
                    <img src="{{ asset('images/static_files/logo.png') }}" alt="{{ config('site.name') }}" style="height: 30px; width: auto; margin-right: 8px;">
                    {{ config('site.name') }}
                </a>
                <div class="d-none d-md-flex">
                    <a href="#features" class="nav-link-custom">Features</a>
                    <a href="#superstars" class="nav-link-custom">Superstars</a>
                    <a href="#stats" class="nav-link-custom">Stats</a>
                    <a href="#cta" class="nav-link-custom">Get Started</a>
                </div>
                <div>
                    @if (Route::has('admin.login'))
                        @auth
                            <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary btn-sm rounded-pill">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('admin.login') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="hero">
        <div class="hero-particles" id="particles"></div>
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-12">
                    <div class="hero-content text-center">
                        <div class="hero-buttons mb-4" data-aos="fade-up" data-aos-delay="200">
                            <a href="#features" class="btn-hero btn-primary-hero btn-lg me-3">
                                <i class="bi bi-play-circle me-2"></i>Start Streaming
                            </a>
                            <a href="#superstars" class="btn-hero btn-secondary-hero btn-lg">
                                <i class="bi bi-stars me-2"></i>Watch Superstars
                            </a>
                        </div>
                        <div class="hero-social-proof" data-aos="fade-up" data-aos-delay="300">
                            <div class="d-flex justify-content-center align-items-center flex-wrap">
                                <span class="badge bg-dark text-white me-3 mb-2">
                                    <i class="bi bi-people-fill me-2"></i>10,000+ Creators
                                </span>
                                <span class="badge bg-dark text-white me-3 mb-2">
                                    <i class="bi bi-star-fill me-2"></i>500+ Superstars
                                </span>
                                <span class="badge bg-dark text-white mb-2">
                                    <i class="bi bi-broadcast me-2"></i>Live 24/7
                                </span>
                            </div>
                        </div>
                        <div class="scroll-indicator mt-5" data-aos="fade-up" data-aos-delay="600">
                            <div class="bounce-animation">
                                <i class="bi bi-chevron-down text-white fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" data-aos="fade-up">
                    Why Choose {{ config('site.name') }}?
                </h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Everything you need to succeed as a content creator
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-camera-video"></i>
                        </div>
                        <h3 class="feature-title">HD Streaming</h3>
                        <p class="feature-description">
                            Stream in crystal-clear 1080p HD with our advanced streaming technology. 
                            Low latency, high quality, zero buffering.
                        </p>
                        <div class="hover-description">
                            <span class="hover-text">Direct creator engagement platform</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h3 class="feature-title">Monetization</h3>
                        <p class="feature-description">
                            Multiple revenue streams including subscriptions, donations, 
                            and ad revenue. Get paid what you deserve.
                        </p>
                        <div class="hover-description">
                            <span class="hover-text">Professional-grade streaming infrastructure</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="feature-title">Community</h3>
                        <p class="feature-description">
                            Build a loyal community with chat, polls, and interactive features. 
                            Engage with your audience in real-time.
                        </p>
                        <div class="hover-description">
                            <span class="hover-text">Exclusive content you won't find anywhere else</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3 class="feature-title">Analytics</h3>
                        <p class="feature-description">
                            Detailed insights into your performance. Track growth, 
                            engagement, and revenue with professional analytics.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="feature-title">Security</h3>
                        <p class="feature-description">
                            Enterprise-grade security to protect your content and community. 
                            Safe, secure, and reliable streaming platform.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="700">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3 class="feature-title">24/7 Support</h3>
                        <p class="feature-description">
                            Round-the-clock support from our dedicated team. 
                            We're here to help you succeed every step of the way.
                        </p>
                        <div class="hover-description">
                            <span class="hover-text">Exclusive content you won't find anywhere else</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Superstars Section -->
    <section class="superstars-section py-5" id="superstars" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3" data-aos="fade-up">
                    Stream Your Superstars
                </h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Connect with your favorite creators and watch exclusive content
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="superstar-avatar">
                                <img src="{{ asset('images/static_files/livestreaming.png') }}" alt="Live Streams">
                            </div>
                            <h5 class="card-title">Live Streams</h5>
                          
                          
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="superstar-avatar">
                                <img src="{{ asset('images/static_files/exclusive.jpg') }}" alt="Exclusive Content">
                            </div>
                            <h5 class="card-title">Exclusive Content</h5>
                             
                          
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="superstar-avatar">
                                <img src="{{ asset('images/static_files/directinteraction.png') }}" alt="Direct Interaction">
                            </div>
                            <h5 class="card-title">Direct Interaction</h5>
                           
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="500">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <img src="{{ asset('images/static_files/logo.png') }}" alt="{{ config('site.name') }}" style="height: 60px; width: auto; margin-right: 15px;">
                    <h3 class="mb-0">Join the Superstar Community</h3>
                </div>
                <p class="text-muted mb-4">
                    Discover amazing creators, support their work, and become part of the streaming revolution
                </p>
                <div>
                    <a href="#cta" class="btn btn-primary btn-lg rounded-pill me-3">
                        <i class="bi bi-play-circle me-2"></i>Start Watching
                    </a>
                    <a href="#features" class="btn btn-outline-primary btn-lg rounded-pill">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section" id="stats">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6" data-aos="fade-up">
                    <div class="stat-item">
                        <div class="stat-number" data-counter="10000">0</div>
                        <div class="stat-label">Active Streamers</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-number" data-counter="5000000">0</div>
                        <div class="stat-label">Monthly Viewers</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-number" data-counter="25000000">0</div>
                        <div class="stat-label">Hours Streamed</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-number" data-counter="99">0</div>
                        <div class="stat-label">% Uptime</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="cta">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="cta-title" data-aos="fade-up">
                        Ready to Start Your Streaming Journey?
                    </h2>
                    <p class="cta-description" data-aos="fade-up" data-aos-delay="100">
                        Join thousands of creators who are already building their audience 
                        and earning money with {{ config('site.name') }}.
                    </p>
                    <div data-aos="fade-up" data-aos-delay="200">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-hero btn-primary-hero btn-lg">
                                <i class="bi bi-rocket-takeoff me-2"></i>Start Free Today
                            </a>
                        @else
                            <a href="#features" class="btn-hero btn-primary-hero btn-lg">
                                <i class="bi bi-play-circle me-2"></i>Get Started
                            </a>
                        @endif
                        <a href="#features" class="btn-hero btn-secondary-hero btn-lg">
                            <i class="bi bi-info-circle me-2"></i>Explore Features
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white py-4" style="background: rgba(0,0,0,0.2);">
        <div class="container text-center">
            <p class="mb-0">
                &copy; 2024 {{ config('site.name') }}. {{ config('site.tagline') }}.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Hide loading screen
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loadingSpinner').style.opacity = '0';
                setTimeout(function() {
                    document.getElementById('loadingSpinner').style.display = 'none';
                }, 500);
            }, 1000);
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Generate particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('[data-counter]');
            const speed = 200;

            counters.forEach(counter => {
                const animate = () => {
                    const target = +counter.getAttribute('data-counter');
                    const count = +counter.innerText.replace(/[^0-9]/g, '');
                    const increment = target / speed;

                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(animate, 10);
                    } else {
                        counter.innerText = target.toLocaleString();
                    }
                };

                // Start animation when element is in view
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animate();
                            observer.unobserve(entry.target);
                        }
                    });
                });

                observer.observe(counter);
            });
        }

        // Initialize
        createParticles();
        animateCounters();
    </script>
</body>
</html>