@props(['value' => false])

@php
    $items = $items ?? [];

    // Последний слайд — приглашение в общий список статей. Выглядит как
    // обычный слайд, но ведёт не на статью, а на /posts.
    $cta = $cta ?? null;

    $total = count($items) + ($cta ? 1 : 0);
@endphp

@if($total)
    <div class="main-slider">
        <div class="main-slider__slider splide" aria-labelledby="Слайдер со статьями">
            <div class="splide__arrows main-slider__arrows">
                <x-arrow-button direction="prev" class="main-slider__arrow main-slider__arrow-prev splide__arrow splide__arrow--prev" label="Предыдущая статья" />

                <div class="main-slider__slide-count">1 / {{ $total }}</div>

                <x-arrow-button direction="next" class="main-slider__arrow main-slider__arrow-next splide__arrow splide__arrow--next" label="Следующая статья" />
            </div>

            <div class="splide__track">
                <ul class="main-slider__list splide__list">
                    @foreach($items as $item)
                        <li class="main-slider__slide splide__slide">
                            <div class="main-slider__slide-container container">
                                <div class="main-slider__row row">
                                    <div class="main-slider__content col-12">
                                        <h2 class="main-slider__title">{{ $item['title'] ?? '' }}</h2>
                                        <p class="main-slider__subtitle">{{ $item['subtitle'] ?? '' }}</p>
                                        <a class="main-slider__article-link button button_transparent" href="{{ $item['link'] ?? '#' }}">Читать статью</a>
                                    </div>
                                    <x-graphic.remote-picture :value="[
                                        'main' => $item['image'] ?? null,
                                        'mobile' => $item['image_mobile'] ?? null,
                                        'tablet' => $item['image_tablet'] ?? null,
                                        'alt' => $item['title'] ?? '',
                                        'class' => 'main-slider__image image',
                                        'eager' => $loop->first,
                                    ]" />
                                </div>
                            </div>
                        </li>
                    @endforeach

                    @if($cta)
                        <li class="main-slider__slide main-slider__slide_cta splide__slide">
                            <div class="main-slider__slide-container container">
                                <div class="main-slider__row row">
                                    <div class="main-slider__content col-12">
                                        <h2 class="main-slider__title">{{ $cta['title'] ?? '' }}</h2>
                                        <p class="main-slider__subtitle">{{ $cta['subtitle'] ?? '' }}</p>
                                        <a class="main-slider__article-link button button_transparent"
                                           href="{{ $cta['link'] ?? '#' }}">{{ $cta['button'] ?? 'Все статьи' }}</a>
                                    </div>
                                    <x-graphic.remote-picture :value="[
                                        'main' => $cta['image'] ?? null,
                                        'mobile' => $cta['image_mobile'] ?? null,
                                        'tablet' => $cta['image_tablet'] ?? null,
                                        'alt' => $cta['title'] ?? '',
                                        'class' => 'main-slider__image image',
                                    ]" />
                                </div>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif