<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon">
                           <img src="{{ asset('images/static_files/logo.png') }}" alt="Logo" class="logo-image">

            </div>
            <span class="logo-text">{{ config('site.name',) }}</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.superstars.index') }}" class="nav-link {{ request()->routeIs('admin.superstars.*') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 10.26 23.77 10.27 17.39 16.88 19.48 25.07 12 19.46 4.52 25.07 6.61 16.88 0.23 10.27 8.91 10.26 12 2"></polygon>
                        </svg>
                    </span>
                    <span class="nav-text">Superstars</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.admins.index') }}" class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 4.354a4 4 0 1 1 0 8.708M12 4.354a4 4 0 0 0-8.708 0M4.581 8.581a4 4 0 0 1 8.708 0M4.581 8.581a4 4 0 0 0-8.708 0"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                    <span class="nav-text">Admin Users</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.finance.index') }}" class="nav-link {{ request()->routeIs('admin.finance.*') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <path d="M1 12h4m0-6h4m0 12h4m-4-8h4m-4 4h4m-4 4h4"></path>
                        </svg>
                    </span>
                    <span class="nav-text">Finance</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.usergoogles.index') }}" class="nav-link {{ request()->routeIs('admin.usergoogles.*') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </span>
                    <span class="nav-text">App Users</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

