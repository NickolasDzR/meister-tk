@props(['value' => false])

<div {{ $attributes->merge(['class' => 'contacts ' . ($value['parentClass'] ?? '')]) }}>
    <ul class="contacts__list">
        @foreach(($value['items'] ?? []) as $item)
            <li class="contacts__item">
                <a class="contacts__link-title" href="{{ $item['titleLink'] ?? '#' }}">{{ $item['title'] ?? '' }}</a>
                @if($item['subtitle'] ?? false)
                    <a class="contacts__link-subtitle" href="{{ $item['subtitleLink'] ?? '#' }}">{{ $item['subtitle'] }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</div>