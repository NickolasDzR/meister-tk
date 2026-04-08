@props(['value' => false, 'nav' => [], 'soc' => [], 'contacts' => []])

<header class="header">
    <div class="container">
        <div class="row">

            @include('modules.logo.logo', ['parentClass' => 'header__logo'])

            <div class="header__tab-nav d-none d-md-flex">
                @include('modules.nav.nav', ['parentClass' => 'header__nav d-none d-xl-block', 'items' => $nav])
                @include('modules.soc.soc', ['parentClass' => 'header__logo', 'items' => $soc])
                @include('modules.contacts.contacts', ['parentClass' => 'header__contacts', 'items' => $contacts])
            </div>

            <x-hamburger :class="'header__hamburger'" />

            <div class="header__mob-nav d-xl-none js-hamburger-activator">
                @include('modules.nav.nav', ['parentClass' => 'header__nav', 'items' => $nav])
                @include('modules.contacts.contacts', ['parentClass' => 'header__contacts', 'items' => $contacts])
                @include('modules.soc.soc', ['parentClass' => 'header__logo', 'items' => $soc])
            </div>
        </div>
    </div>
</header>

{{--
-- Использование (модуль):
-- <x-header :nav="$navigationArray" :soc="$socialArray" :contacts="$contactsArray" />
--
-- Пример данных:
-- @php
--     $nav = [['title' => 'Главная', 'link' => '/'], ['title' => 'О нас', 'link' => '/about']];
--     $soc = [['name' => 'fb', 'link' => '#'], ['name' => 'vk', 'link' => '#']];
--     $contacts = [['title' => 'Email', 'titleLink' => 'mailto:test@mail.com']];
-- @endphp
-- <x-header :nav="$nav" :soc="$soc" :contacts="$contacts" />
--}}