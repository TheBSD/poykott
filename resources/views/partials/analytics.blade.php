<!-- Google tag (gtag.js) -->
@if (config('services.google.analytics_id'))
    <script
        async
        src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"
    ></script>
@endif

<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', '{{ config('services.google.analytics_id') }}', {
        anonymize_ip: true, // todo look at this option. if not useful, then remove
    });
</script>
<!-- End Google Analytics -->
