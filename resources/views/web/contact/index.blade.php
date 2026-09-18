@extends('web.layouts.app')

@section('title', 'Contact Us - ' . ($company->name ?? config('app.name')))

@section('content')
<main>
    <section class="page-hero">
        <div class="page-hero__bg" style="background-image: url('{{ asset('assets/admin/img/backgrounds/10.jpg') }}');"></div>
        <div class="page-hero__overlay"></div>
        <div class="page-hero__content">
            <p class="kicker">Get in Touch</p>
            <h1>Contact Us</h1>
            <p>We'd love to hear from you. Reach out for bookings, inquiries, or feedback.</p>
        </div>
    </section>

    <section class="section-pad">
        <div class="section-shell">
            <div class="contact-grid">
                <div>
                    <h2 style="margin: 0 0 24px; font-size: 26px;">Send Us a Message</h2>
                    <form class="contact-form" id="contact-form">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <label>
                                <span>Full Name *</span>
                                <input type="text" name="name" placeholder="Your full name" required>
                            </label>
                            <label>
                                <span>Email *</span>
                                <input type="email" name="email" placeholder="you@example.com" required>
                            </label>
                        </div>
                        <label>
                            <span>Phone</span>
                            <input type="tel" name="phone" placeholder="Phone number">
                        </label>
                        <label>
                            <span>Subject</span>
                            <select name="subject">
                                <option value="general">General Inquiry</option>
                                <option value="booking">Booking Question</option>
                                <option value="feedback">Feedback</option>
                                <option value="corporate">Corporate Bookings</option>
                                <option value="events">Events & Banquets</option>
                            </select>
                        </label>
                        <label>
                            <span>Message *</span>
                            <textarea name="message" placeholder="How can we help you?" required></textarea>
                        </label>
                        <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
                    </form>
                    <div id="contact-success" style="display: none; padding: 18px; background: rgba(20,98,79,0.08); border: 1px solid rgba(20,98,79,0.2); color: #14624f; font-weight: 600; margin-top: 16px;">
                        <i class="fas fa-check-circle"></i> Thank you! Your message has been received. We'll get back to you within 24 hours.
                    </div>
                </div>

                <div>
                    <div class="contact-info-card">
                        <h4><i class="fas fa-map-marker-alt" style="color: #bd8c3a; margin-right: 8px;"></i> Address</h4>
                        @if($company && ($company->address || $company->city || $company->country))
                            <p>{{ collect([$company->address, $company->city, $company->state, $company->zipcode, $company->country])->filter()->implode('<br>') }}</p>
                        @else
                            <p>Contact us for our office address.</p>
                        @endif
                    </div>

                    <div class="contact-info-card">
                        <h4><i class="fas fa-phone" style="color: #bd8c3a; margin-right: 8px;"></i> Phone</h4>
                        @if($company?->phone)
                            <p>{{ $company->phone }} (Reservations)</p>
                        @else
                            <p>Contact us for phone details.</p>
                        @endif
                    </div>

                    <div class="contact-info-card">
                        <h4><i class="fas fa-envelope" style="color: #bd8c3a; margin-right: 8px;"></i> Email</h4>
                        @if($company?->email)
                            <p>{{ $company->email }}</p>
                        @else
                            <p>Contact us for email details.</p>
                        @endif
                    </div>

                    <div class="contact-info-card">
                        <h4><i class="fas fa-clock" style="color: #bd8c3a; margin-right: 8px;"></i> Hours</h4>
                        <p>Front Desk: 24/7</p>
                        <p>Reservations: 8 AM - 10 PM</p>
                        <p>Restaurant: 7 AM - 11 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-band">
        <h2>Ready to Book?</h2>
        <p>Skip the phone call and book your room online for instant confirmation.</p>
        <a href="{{ route('web.booking') }}" class="btn-primary-site" style="background: #fff; color: #14624f;">
            <i class="fas fa-search"></i> Search & Book
        </a>
    </section>
</main>
@endsection

@section('script')
<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    document.getElementById('contact-success').style.display = 'block';
    this.reset();
    window.scrollTo({ top: document.getElementById('contact-success').offsetTop - 100, behavior: 'smooth' });
});
</script>
@endsection
