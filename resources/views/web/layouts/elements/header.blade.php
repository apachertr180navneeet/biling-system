<nav class="site-nav" id="site-nav">
    <div class="site-nav__inner">
        <a class="site-nav__brand" href="{{ route('/') }}">
            <i class="fas fa-hotel"></i>
            <span>{{ $company->name ?? config('app.name') }}</span>
        </a>

        <button class="site-nav__toggle" id="nav-toggle" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <div class="site-nav__menu" id="nav-menu">
            <a href="{{ route('/') }}" class="{{ request()->routeIs('/') ? 'is-active' : '' }}">Home</a>
            <a href="{{ route('web.hotels') }}" class="{{ request()->routeIs('web.*') && !request()->routeIs('web.booking*') ? 'is-active' : '' }}">Hotels</a>
            <a href="{{ route('web.booking') }}" class="{{ request()->routeIs('web.booking*') ? 'is-active' : '' }}">Book Now</a>
            <a href="{{ route('web.about') }}" class="{{ request()->routeIs('web.about') ? 'is-active' : '' }}">About</a>
            <a href="{{ route('web.contact') }}" class="{{ request()->routeIs('web.contact') ? 'is-active' : '' }}">Contact</a>
            <a href="{{ route('guest.login') }}" class="{{ request()->routeIs('guest.*') ? 'is-active' : '' }}">Guest Portal</a>
            <a href="{{ route('staff.login') }}" class="site-nav__cta">Staff Login</a>
        </div>
    </div>
</nav>

<script>
document.getElementById('nav-toggle').addEventListener('click', function() {
    document.getElementById('nav-menu').classList.toggle('is-open');
    this.querySelector('i').classList.toggle('fa-bars');
    this.querySelector('i').classList.toggle('fa-times');
});
</script>
