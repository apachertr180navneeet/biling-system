<footer class="content-footer footer bg-footer-theme">
    <div class="container-fluid d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
            ©
            <script>
                document.write(new Date().getFullYear());
            </script>
            <a href="{{ route('admin.dashboard') }}" class="footer-link fw-medium">{{ config('app.name') }}</a>
            — All rights reserved.
        </div>
        <div class="d-none d-lg-inline-block">
            <span class="text-muted" style="font-size: 0.8125rem;">v1.0</span>
        </div>
    </div>
</footer>