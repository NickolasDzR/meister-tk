{{--
-- Разметка Schema.org: статья. Поисковик берёт отсюда заголовок, обложку
-- и даты для карточки в выдаче. Автор и издатель — компания: @id связывает
-- их с разметкой организации из layout, а имя и адрес Google требует
-- видеть прямо здесь.
--}}
@php
    $organization = [
        '@type' => 'Organization',
        '@id' => url('/') . '/#organization',
        'name' => config('site.name'),
        'url' => url('/'),
    ];

    $article = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'mainEntityOfPage' => route('post', $post->slug),
        'headline' => $post->title,
        'description' => $post->excerpt,
        // jpg для превью, если загружен, иначе обложка — как в og:image.
        'image' => $post->ogImageUrl(),
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => ($post->updated_at ?? $post->published_at)?->toIso8601String(),
        'author' => $organization,
        'publisher' => $organization,
        'inLanguage' => 'ru',
    ]);
@endphp
<script type="application/ld+json">
@json($article, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</script>
