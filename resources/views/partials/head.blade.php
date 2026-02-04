<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#10b981">

<title>{{ $title ?? \App\Helpers\Settings::businessConfiguration('business_name', config('app.name')) }}</title>
<meta name="description" content="Sweet Tooth Point of Sale and Management System">

<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">

<!-- Favicons -->
@php
    use Illuminate\Support\Facades\Storage;

    $logoPath = \App\Helpers\Settings::businessConfiguration('logo_upload');
    $hasLogo = $logoPath && Storage::disk('public')->exists($logoPath);
    $cacheBuster = $hasLogo ? Storage::disk('public')->lastModified($logoPath) : 'default';
    $faviconUrl = $hasLogo ? Storage::disk('public')->url($logoPath) : '/favicon.ico';
@endphp
<link rel="icon" href="{{ $faviconUrl }}?v={{ $cacheBuster }}" sizes="any">
<link rel="icon" href="{{ $faviconUrl }}?v={{ $cacheBuster }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ $faviconUrl }}?v={{ $cacheBuster }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<tallstackui:script />
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
