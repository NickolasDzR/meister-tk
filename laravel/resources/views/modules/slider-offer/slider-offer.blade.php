@php
    use Illuminate\Support\Facades\Storage;
    use App\Models\Post;

    $slides = $slides ?? Post::where('published', true)
        ->whereNotNull('image')
        ->inRandomOrder()
        ->limit(8)
        ->get()
        ->map(fn($post) => [
            'image' => Storage::disk('yandex')->url($post->image),
            'title' => $post->title,
            'link'  => route('post', $post->slug),
        ])
        ->toArray();
@endphp

@if(count($slides))
    <section class="slider-offer">
        <div class="slider-offer__splide splide" aria-label="Другие статьи">
            <div class="splide__track">
                <ul class="slider-offer__list splide__list">
                    @foreach($slides as $slide)
                        <li class="slider-offer__slide splide__slide">
                            <a href="{{ $slide['link'] }}" class="slider-offer__link">
                                <img
                                    class="slider-offer__img"
                                    src="{{ $slide['image'] }}"
                                    alt="{{ $slide['title'] }}"
                                >
                                <span class="slider-offer__title">{{ $slide['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="splide__arrows slider-offer__arrows">
                <button class="slider-offer__arrow slider-offer__arrow_prev splide__arrow splide__arrow--prev" aria-label="Назад">
                    <svg viewBox="0 0 12 20" width="12" height="20" fill="none">
                        <path d="M11 1L2 10L11 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <span class="slider-offer__counter"></span>
                <button class="slider-offer__arrow slider-offer__arrow_next splide__arrow splide__arrow--next" aria-label="Вперёд">
                    <svg viewBox="0 0 12 20" width="12" height="20" fill="none">
                        <path d="M1 1L10 10L1 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>
@endif
