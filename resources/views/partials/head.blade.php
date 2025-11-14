<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#10b981">

<title>{{ $title ?? config('app.name') }}</title>
<meta name="description" content="Sweet Tooth Point of Sale and Management System">

<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">

<!-- Favicons -->
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<tallstackui:script />
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<!-- PWA Script -->
<script src="/js/pwa.js" defer></script>
