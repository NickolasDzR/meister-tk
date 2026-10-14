@props(['title' => '', 'button' => ''])

<div class="cargo-calc">
    <x-close-button class="cargo-calc__close-button" />

    <div class="cargo-calc__form-wrapper">
        {{-- h1 страницы: кто мы и что делаем. На главной это единственный
             заголовок про компанию — заголовки слайдов говорят про статьи. --}}
        <h1 class="cargo-calc__title">{{ $title }}</h1>

        <div class="cargo-calc__form-inner">
            <div class="cargo-calc__delivery-results">
                <div class="cargo-calc__delivery-results-wrapper"></div>
                <a class="button button_keppel cargo-calc__call" href="{{ config('site.phone.href') }}">Позвонить менеджеру</a>
                <x-button type="transparent" class="cargo-calc__button-results" text="Посчитать заново" />
            </div>

            <form class="cargo-calc__form" data-phone="{{ config('site.phone.display') }}" data-phone-href="{{ config('site.phone.href') }}">
                <div class="cargo-calc__content">
                    <x-select
                            :options="['Выберите место загрузки']"
                            error="Введите место загрузки"
                            parentClass="cargo-calc__input"
                            icon="location"
                            name="location_0"
                    />

                    <x-select
                            :options="['Выберите место выгрузки']"
                            error="Введите место выгрузки"
                            parentClass="cargo-calc__input"
                            icon="location"
                            name="location_1"
                    />

                    <x-input
                            placeholder="Вес груза, т"
                            error="Введите вес груза"
                            class="cargo-calc__input"
                            placeholderClass="cargo-calc__placeholder"
                            icon="weight"
                            iconClass="cargo-calc__placeholder-icon"
                            name="weight"
                            inputmode="decimal"
                    />

                    <x-button type="keppel" class="cargo-calc__button" text="Рассчитать доставку" />
                </div>
            </form>
        </div>
    </div>
</div>