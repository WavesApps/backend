<header class="top-navbar" id="topNavbar">
    <div class="navbar-left">
       
        <button class="navbar-toggle" id="sidebarToggleBtn" title="Toggle sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </div>
    
    <div class="navbar-center">
        <div class="search-bar">
            <input type="text" placeholder="Search... (Ctrl+K)" class="search-input" id="searchInput">
        </div>
    </div>
    
    <div class="navbar-right">
        <div class="navbar-actions">
            <button class="navbar-action-btn" id="fullscreenToggle" title="Toggle fullscreen">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                </svg>
            </button>
        </div>
        
        @include('adminpages.layouts.partials.user-dropdown')
    </div>
</header>

