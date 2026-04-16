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
        </div>
    </section>
@endif
