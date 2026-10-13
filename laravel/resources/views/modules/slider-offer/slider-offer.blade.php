@php
    use Illuminate\Support\Facades\Storage;
    use App\Models\Post;

    $slides = $slides ?? Post::published()
        ->whereNotNull('image')
        ->when(isset($post), fn ($query) => $query->whereKeyNot($post->id))
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
        <h2 class="slider-offer__heading">Другие статьи</h2>

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
                <x-arrow-button direction="prev" class="slider-offer__arrow slider-offer__arrow_prev splide__arrow splide__arrow--prev" label="Назад" />
                <span class="slider-offer__counter"></span>
                <x-arrow-button direction="next" class="slider-offer__arrow slider-offer__arrow_next splide__arrow splide__arrow--next" label="Вперёд" />
            </div>
        </div>
    </section>
@endif
