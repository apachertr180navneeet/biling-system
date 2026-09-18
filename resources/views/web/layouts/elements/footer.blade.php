<footer class="site-footer">
    <div class="site-footer__inner">
        <div class="site-footer__brand">
            <h3><i class="fas fa-hotel"></i> {{ $company->name ?? config('app.name') }}</h3>
            <p>{{ $company->tagline ?? 'Premium hotel management and direct booking engine.' }}</p>
            <div class="site-footer__social">
                @if($company?->facebook_url)
                    <a href="{{ $company->facebook_url }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
                @endif
                @if($company?->twitter_url)
                    <a href="{{ $company->twitter_url }}" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>
                @endif
                @if($company?->instagram_url)
                    <a href="{{ $company->instagram_url }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                @endif
                @if($company?->linkedin_url)
                    <a href="{{ $company->linkedin_url }}" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a>
                @endif
                @if(! $company?->facebook_url && ! $company?->twitter_url && ! $company?->instagram_url && ! $company?->linkedin_url)
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                @endif
            </div>
        </div>

        <div class="site-footer__links">
            <h4>Quick Links</h4>
            <a href="{{ route('/') }}">Home</a>
            <a href="{{ route('web.hotels') }}">Hotels</a>
            <a href="{{ route('web.booking') }}">Book Now</a>
            <a href="{{ route('web.about') }}">About Us</a>
            <a href="{{ route('web.contact') }}">Contact</a>
        </div>

        <div class="site-footer__links">
            <h4>Services</h4>
            <a href="{{ route('web.booking') }}">Direct Booking</a>
            <a href="#">Room Management</a>
            <a href="#">Event Planning</a>
            <a href="#">Spa & Wellness</a>
            <a href="#">Restaurant</a>
        </div>

        <div class="site-footer__contact">
            <h4>Contact Us</h4>
            @if($company && ($company->address || $company->city || $company->country))
                <p><i class="fas fa-map-marker-alt"></i> {{ collect([$company->address, $company->city, $company->country])->filter()->implode(', ') }}</p>
            @else
                <p><i class="fas fa-map-marker-alt"></i> Contact us for our office address</p>
            @endif
            @if($company?->phone)
                <p><i class="fas fa-phone"></i> {{ $company->phone }}</p>
            @endif
            @if($company?->email)
                <p><i class="fas fa-envelope"></i> {{ $company->email }}</p>
            @endif
            <p><i class="fas fa-clock"></i> 24/7 Front Desk</p>
        </div>
    </div>

    <div class="site-footer__bottom">
        <p>&copy; {{ date('Y') }} {{ $company->name ?? config('app.name') }}. All rights reserved.</p>
    </div>
</footer>
