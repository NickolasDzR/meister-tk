@props(['value' => false])

<footer class="footer {{ !request()->routeIs('posts') ? 'footer_bordered' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-12 col-xl-5">
                <p class="footer__address">
                    ООО «Мейстер» , 606007, Нижегородская область, г. Дзержинск, Водозаборная, 3а.
                </p>
            </div>
            <div class="col-2"></div>
            <div class="col-12 col-xl-5 d-flex justify-content-xl-end align-items-center">
                <ul class="footer__list">
                    <li class="footer__item">
                        <a href="#" class="footer__link">
                            Контакты
                        </a>
                    </li>
                    <li class="footer__item">
                        <a href="#" class="footer__link">
                            Новости
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>