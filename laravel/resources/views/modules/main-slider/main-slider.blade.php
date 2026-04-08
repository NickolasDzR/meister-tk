@props(['value' => false])

@php
    $items = $items ?? [];
    $total = count($items);
@endphp

@if($total)
    <div class="main-slider">
        <div class="main-slider__slider splide" aria-labelledby="Слайдер со статьями">
            <div class="splide__arrows main-slider__arrows">
                <button class="main-slider__arrow main-slider__arrow-prev splide__arrow splide__arrow--prev">
                    <svg class="main-slider__svg-arrow" viewBox="0 0 12 20" width="12" height="20">
                        <path class="main-slider__svg-arrow-path" d="M11.0283 1.20798L2.39014 9.84619L11.0283 18.4844"></path>
                    </svg>
                </button>

                <div class="main-slider__slide-count">1 / {{ $total }}</div>

                <button class="main-slider__arrow main-slider__arrow-next splide__arrow splide__arrow--next">
                    <svg class="main-slider__svg-arrow" viewBox="0 0 12 20" width="12" height="20">
                        <path class="main-slider__svg-arrow-path" d="M1.39011 1.26797L10.0283 9.90619L1.39011 18.5444"></path>
                    </svg>
                    <svg class="splide__progress main-slider__progress" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <circle class="main-slider__progress-bar" cx="50" cy="50" r="50"></circle>
                    </svg>
                </button>
            </div>

            <div class="splide__track">
                <ul class="main-slider__list splide__list">
                    @foreach($items as $item)
                        <li class="main-slider__slide splide__slide">
                            <div class="main-slider__slide-container container">
                                <div class="main-slider__row row">
                                    <div class="main-slider__content col-12">
                                        <div class="main-slider__title">{{ $item['title'] ?? '' }}</div>
                                        <div class="main-slider__subtitle">{{ $item['subtitle'] ?? '' }}</div>
                                        <a class="main-slider__article-link button button_transparent" href="#">Подробнее</a>
                                    </div>
                                    <x-graphic.picture :value="[
                                        'name' => $item['images']['mobile'],
                                        'alt' => $item['title'],
                                        'class' => 'main-slider__image image',
                                        'md' => true,  // будет использоваться планшетная версия
                                    ]" />
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif