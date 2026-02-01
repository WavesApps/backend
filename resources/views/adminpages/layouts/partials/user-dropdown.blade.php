<div class="user-profile-dropdown" id="userDropdown">
    <button class="user-profile-trigger" id="userDropdownToggle">
        <div class="user-avatar">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>
        <span class="user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
        <svg class="dropdown-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>
    
    <div class="user-dropdown-menu" id="userDropdownMenu">
        <a href="{{ route('profile.edit') }}" class="dropdown-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Profile</span>
        </a>
        <form action="{{ route('admin.logout') }}" method="POST" class="dropdown-item-form">
            @csrf
            <button type="submit" class="dropdown-item logout-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>

