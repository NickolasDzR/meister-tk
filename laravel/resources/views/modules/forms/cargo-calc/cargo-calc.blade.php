@props(['title' => '', 'button' => ''])

<div class="cargo-calc">
    <x-close-button class="cargo-calc__close-button" />

    <div class="cargo-calc__form-wrapper">
        <p class="cargo-calc__title">{{ $title }}</p>

        <div class="cargo-calc__form-inner">
            <div class="cargo-calc__delivery-results">
                <div class="cargo-calc__delivery-results-wrapper"></div>
                <x-button type="keppel" class="cargo-calc__button-results" text="Понятно" />
            </div>

            <form class="cargo-calc__form">
                <div class="cargo-calc__content">
                    <x-select
                            :options="['Выберете место загрузки']"
                            error="Введите значение"
                            parentClass="cargo-calc__input"
                            icon="location"
                            name="location_0"
                    />

                    <x-select
                            :options="['Выберете место выгрузки']"
                            error="Введите значение"
                            parentClass="cargo-calc__input"
                            icon="location"
                            name="location_1"
                    />

                    <x-input
                            placeholder="Тоннаж"
                            error="Введите значение"
                            class="cargo-calc__input"
                            placeholderClass="cargo-calc__placeholder"
                            icon="weight"
                            iconClass="cargo-calc__placeholder-icon"
                            name="weight"
                    />

                    <x-button type="keppel" class="cargo-calc__button" text="Рассчитать доставку" />
                </div>
            </form>
        </div>
    </div>
</div>