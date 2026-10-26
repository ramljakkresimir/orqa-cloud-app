<nav class="cloud-nav" aria-label="Cloud navigation">
    <a class="cloud-nav-link {{ request()->routeIs('user.dashboard', 'admin.dashboard') ? 'active' : '' }}"
       href="{{ route($dashboardRoute) }}">
        <i class="bi bi-grid-1x2" aria-hidden="true"></i> Dashboard
    </a>
    <a class="cloud-nav-link {{ request()->routeIs('files.index') ? 'active' : '' }}"
       href="{{ route('files.index') }}">
        <i class="bi bi-folder2-open" aria-hidden="true"></i> {{ $isAdmin ? 'All Files' : 'My Files' }}
    </a>
    @if ($isAdmin)
        <a class="cloud-nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}"
           href="{{ route('admin.users.index') }}">
            <i class="bi bi-people" aria-hidden="true"></i> Users
        </a>
    @else
        <a class="cloud-nav-link {{ request()->routeIs('files.create') ? 'active' : '' }}"
           href="{{ route('files.create') }}">
            <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i> Upload File
        </a>
    @endif
    <a class="cloud-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
       href="{{ route('profile.edit') }}">
        <i class="bi bi-person-circle" aria-hidden="true"></i> My Profile
    </a>
</nav>
<form method="POST" action="{{ route('logout') }}" class="cloud-logout mt-auto pt-4">
    @csrf
    <button class="cloud-nav-link cloud-logout-button w-100" type="submit">
        <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Log Out
    </button>
</form>
