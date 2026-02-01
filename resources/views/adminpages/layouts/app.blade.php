<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --sidebar-bg: #ffffff;
            --sidebar-width: 280px;
            --navbar-height: 70px;
            --text-primary: #1a202c;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --hover-bg: #f7fafc;
            --active-bg: #667eea;
            --active-text: #ffffff;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-primary);
            margin: 0;
            padding: 0;
        }
    </style>

    <title>@yield('title', 'Dashboard') | {{ config('site.name', 'Streaming Platform') }}</title>
    <link rel="icon" href="{{ asset('/images/static_files/logo.png') }}" type="image/png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --sidebar-bg: #ffffff;
            --sidebar-width: 280px;
            --navbar-height: 70px;
            --text-primary: #1a202c;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --hover-bg: #f7fafc;
            --active-bg: #667eea;
            --active-text: #ffffff;
        }
        
        body {
            font-family: 'Tahoma', sans-serif;
            background-color: #f5f7fa;
            color: var(--text-primary);
            overflow-x: hidden;
        }
        
        /* Top Navbar */
        .top-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--navbar-height);
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 1000;
            transition: left 0.3s ease;
        }
        
        .navbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .navbar-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }
        
        .navbar-logo-text {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .navbar-toggle {
            background: none;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s;
        }
        
        .navbar-toggle:hover {
            background: var(--hover-bg);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .navbar-center {
            flex: 1;
            max-width: 600px;
            margin: 0 24px;
        }
        
        .search-bar {
            position: relative;
            width: 100%;
        }
        
        .search-input {
            width: 100%;
            padding: 10px 16px 10px 44px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Tahoma', sans-serif;
            background: #ffffff;
            color: var(--text-primary);
            transition: all 0.2s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .search-input::placeholder {
            color: var(--text-secondary);
        }
        
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .navbar-action-btn {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: #ffffff;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .navbar-action-btn:hover {
            background: var(--hover-bg);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        /* User Dropdown */
        .user-profile-dropdown {
            position: relative;
        }
        
        .user-profile-trigger {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .user-profile-trigger:hover {
            background: var(--hover-bg);
            border-color: var(--primary-color);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .dropdown-chevron {
            color: var(--text-secondary);
            transition: transform 0.2s;
        }
        
        .user-profile-dropdown.active .dropdown-chevron {
            transform: rotate(180deg);
        }
        
        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s;
            z-index: 1000;
        }
        
        .user-profile-dropdown.active .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item,
        .dropdown-item-form {
            display: block;
            width: 100%;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s;
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
            text-align: left;
        }
        
        .dropdown-item:hover {
            background: var(--hover-bg);
        }
        
        .dropdown-item svg {
            color: var(--text-secondary);
        }
        
        .logout-item {
            color: #ef4444;
        }
        
        .logout-item svg {
            color: #ef4444;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            z-index: 1100;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            flex-shrink: 0;
        }
        
        .logo-image {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }
        
        .logo-text {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .sidebar-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s;
            flex-shrink: 0;
        }
        
        .sidebar-toggle:hover {
            background: var(--hover-bg);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .sidebar-nav {
            padding: 16px 12px;
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-menu li {
            margin-bottom: 4px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background: var(--hover-bg);
            color: var(--text-primary);
        }
        
        .nav-link.active {
            background: var(--active-bg);
            color: var(--active-text);
        }
        
        .nav-link.active .nav-icon {
            color: var(--active-text);
        }
        
        .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: color 0.2s;
        }
        
        .nav-text {
            flex: 1;
        }
        
        /* Main Content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 24px;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin-left 0.3s ease;
        }
        
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Mobile Styles */
        @media (max-width: 1024px) {
            .navbar-center {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 280px;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .top-navbar {
                left: 0;
            }
            
            .navbar-toggle {
                display: block;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .main-wrapper {
                margin-left: 0;
            }
            
            .main-content {
                padding: 0;
            }
            
            .navbar-logo-text {
                display: none;
            }
            
            .user-name {
                display: none;
            }
            
            .logo-text {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .top-navbar {
                padding: 0 12px;
            }
            
            .navbar-actions {
                gap: 4px;
            }
            
            .navbar-action-btn {
                width: 36px;
                height: 36px;
            }
            
            .main-wrapper {
                padding: 16px;
            }
        }
        
        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }
        
        @media (min-width: 769px) {
            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    @include('adminpages.layouts.partials.sidebar')
    @include('adminpages.layouts.partials.navbar')
    
    <main class="main-wrapper">
        <div class="main-content">
            @yield('content')
        </div>
    </main>
    
    <script>
        // Sidebar Toggle
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }
        
        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', toggleSidebar);
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }
        
        // User Dropdown Toggle
        const userDropdown = document.getElementById('userDropdown');
        const userDropdownToggle = document.getElementById('userDropdownToggle');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        
        if (userDropdownToggle) {
            userDropdownToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (userDropdown && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (userDropdown) {
                    userDropdown.classList.remove('active');
                }
                if (sidebar && window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
        
        // Search functionality (Ctrl+K)
        const searchInput = document.getElementById('searchInput');
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
        
        // Theme Toggle (placeholder)
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                // Theme toggle functionality can be added here
                console.log('Theme toggle clicked');
            });
        }
        
        // Fullscreen Toggle
        const fullscreenToggle = document.getElementById('fullscreenToggle');
        if (fullscreenToggle) {
            fullscreenToggle.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log('Error attempting to enable fullscreen:', err);
                    });
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            });
        }
        
        // Prevent browser from caching this page
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
        
        // Prevent back button from showing cached dashboard
        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href);
            window.addEventListener('popstate', function() {
                window.history.pushState(null, null, window.location.href);
                fetch(window.location.href, {
                    method: 'HEAD',
                    cache: 'no-store',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(response) {
                    if (response.status === 302 || response.redirected) {
                        window.location.href = '{{ route("admin.login") }}';
                    } else {
                        window.location.reload();
                    }
                }).catch(function() {
                    window.location.reload();
                });
            });
        }
    </script>
    
    @yield('scripts')
</body>
</html>

