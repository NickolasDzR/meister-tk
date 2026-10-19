{{--
-- Разметка Schema.org: кто компания. Поисковик читает её с любой страницы
-- и показывает в выдаче название, контакты и соцсети. Данные — из
-- config/site.php, того же места, откуда их берут шапка и подвал.
-- JSON_UNESCAPED_* — чтобы кириллица и адреса читались в исходнике как есть.
--}}
@php
    $address = config('site.address');

    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => url('/') . '/#organization',
        'name' => config('site.name'),
        'legalName' => config('site.legal_name'),
        'url' => url('/'),
        // Квадратный знак без надписи: поисковик показывает логотип
        // маленьким квадратом, длинный «Мейстер» там не читается.
        'logo' => asset('images/content/schema-logo.jpg'),
        'taxID' => config('site.inn'),
        'identifier' => [
            '@type' => 'PropertyValue',
            'propertyID' => 'ОГРН',
            'value' => config('site.ogrn'),
        ],
        'telephone' => str_replace('tel:', '', config('site.phone.href')),
        'email' => config('site.email'),
        'address' => [
            '@type' => 'PostalAddress',
            'postalCode' => $address['postal_code'],
            'addressRegion' => $address['region'],
            'addressLocality' => $address['city'],
            'streetAddress' => $address['street'],
            'addressCountry' => 'RU',
        ],
        'areaServed' => 'RU',
        'sameAs' => array_column(config('site.social'), 'link'),
    ];
@endphp
<script type="application/ld+json">
@json($organization, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</script>
