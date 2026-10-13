/**
 * Раскрытие картинки по клику — PhotoSwipe.
 *
 * Свой <dialog> не годился для телефона: широкую картинку шире экрана
 * не впишешь, и раскрытая выходила меньше, чем в статье. Нужен зум двумя
 * пальцами, двойной тап, перетаскивание увеличенной картинки — это и
 * умеет PhotoSwipe, отлаженный на iOS и Android.
 *
 * Раскрываются только картинки с data-zoom (тумблер «Можно раскрыть»
 * в админке). Картинки с одинаковым значением data-zoom листаются вместе
 * (например, обе из блока «Две картинки»), с пустым — открываются одни.
 * Другие блоки статьи в листание не попадают.
 */

import PhotoSwipeLightbox from 'photoswipe/lightbox';
import type { SlideData } from 'photoswipe';
import { preloader } from '@utils';

const sources = Array.from(document.querySelectorAll<HTMLImageElement>('img[data-zoom]'));

if (sources.length) {
    // Само окно (~17 КБ) грузится только по первому клику: картинку могут
    // и не раскрыть, а страница от этого не тяжелеет. Загрузка одна на всех:
    // повторные клики ждут тот же промис, а не качают файл заново.
    let pswpModule: Promise<typeof import('photoswipe')> | null = null;
    const loadPswp = () => (pswpModule ??= import('photoswipe'));

    const lightbox = new PhotoSwipeLightbox({
        pswpModule: loadPswp,

        bgOpacity: 0.92,

        // Картинка раскрывается из своего места в статье и туда же
        // возвращается при закрытии.
        showHideAnimationType: 'zoom',

        // Двойной тап и кнопка «+» — натуральный размер, 1:1: для скриншота
        // это и есть «прочитать мелкие подписи». Пальцами — до четырёх раз.
        secondaryZoomLevel: 1,
        maxZoomLevel: 4,

        // Кнопки зума нет: на телефоне увеличивают пальцами по привычке,
        // на компьютере — кликом по картинке. Двойной тап и щипок работают.
        zoom: false,

        // Картинка не вплотную к краям экрана — 15px со всех сторон,
        // как боковые поля сайта на телефоне.
        padding: { top: 15, bottom: 15, left: 15, right: 15 },

        // Крестик — такой же, как у меню и формы расчёта: две тонкие белые
        // линии 28px, а не жирный значок библиотеки с обводкой.
        closeSVG: '<svg aria-hidden="true" class="pswp__icn pswp__icn-close" viewBox="0 0 32 32" width="32" height="32">'
            + '<path d="M6 6L26 26M26 6L6 26"/></svg>',

        // Стрелки и счётчик — свои, как в слайдере «Другие статьи» и
        // пагинации (см. uiRegister ниже). Встроенные выключаем.
        arrowPrev: false,
        arrowNext: false,
        counter: false,

        // Без зацикливания: с последней картинки — не на первую, как и
        // в слайдерах сайта; крайняя стрелка становится неактивной.
        loop: false,

        closeTitle: 'Закрыть',
        errorMsg: 'Картинка не загрузилась',
    });

    // Тот же значок стрелки, что рисует компонент arrow-button.
    const arrow = (direction: 'prev' | 'next') => {
        const path = direction === 'prev' ? 'M11 1L2 10L11 19' : 'M1 1L10 10L1 19';

        return `<svg class="arrow-button__svg" viewBox="0 0 12 20" width="12" height="20" fill="none" aria-hidden="true">`
            + `<path class="arrow-button__path" d="${path}"/></svg>`;
    };

    // Навигация: стрелки arrow-button и счётчик «1 / 2» внизу по центру,
    // как было в нашем окне. Классы — те же, что у компонента, поэтому
    // вид и состояния (наведение, неактивная) берутся из arrow-button.scss.
    lightbox.on('uiRegister', () => {
        lightbox.pswp?.ui?.registerElement({
            name: 'nav',
            appendTo: 'wrapper',
            html: `<button type="button" class="arrow-button arrow-button_prev lightbox__arrow lightbox__arrow_prev" aria-label="Предыдущая картинка">${arrow('prev')}</button>`
                + `<span class="lightbox__counter" aria-live="polite"></span>`
                + `<button type="button" class="arrow-button arrow-button_next lightbox__arrow lightbox__arrow_next" aria-label="Следующая картинка">${arrow('next')}</button>`,
            onInit: (element, pswp) => {
                const prev = element.querySelector('.lightbox__arrow_prev') as HTMLButtonElement;
                const next = element.querySelector('.lightbox__arrow_next') as HTMLButtonElement;
                const counter = element.querySelector('.lightbox__counter') as HTMLSpanElement;

                prev.addEventListener('click', () => pswp.prev());
                next.addEventListener('click', () => pswp.next());

                const update = () => {
                    const total = pswp.getNumItems();

                    counter.textContent = `${pswp.currIndex + 1} / ${total}`;
                    prev.disabled = pswp.currIndex === 0;
                    next.disabled = pswp.currIndex === total - 1;
                };

                pswp.on('change', update);
                update();
            },
        });
    });

    // Стрелки и клавиши ← → в PhotoSwipe меняют картинку мгновенно —
    // анимирован только свайп. Переводим их на тот же сдвиг ленты, что
    // и у свайпа: одна картинка уезжает, другая приезжает.
    lightbox.on('init', () => {
        const pswp = lightbox.pswp;

        if (!pswp) {
            return;
        }

        pswp.next = () => { pswp.mainScroll.moveIndexBy(1, true); };
        pswp.prev = () => { pswp.mainScroll.moveIndexBy(-1, true); };
    });

    lightbox.init();

    const groupOf = (source: HTMLImageElement): HTMLImageElement[] => {
        const name = source.dataset.zoom;

        return name ? sources.filter(item => item.dataset.zoom === name) : [source];
    };

    // Размеры PhotoSwipe нужны заранее — берём у картинки в статье: это тот
    // же файл, браузер его уже загрузил.
    const toSlide = (img: HTMLImageElement): SlideData => ({
        src: img.currentSrc || img.src,
        msrc: img.currentSrc || img.src,
        width: img.naturalWidth,
        height: img.naturalHeight,
        alt: img.alt,
        // Откуда раскрывать. В статье картинка обрезана (object-fit: cover),
        // thumbCropped говорит PhotoSwipe учесть это в анимации.
        element: img,
        thumbCropped: true,
    });

    // Окно уже открывается — повторные тапы игнорируем. Без этого, пока
    // грузилась библиотека, человек тапал ещё и ещё, ничего не видя.
    let opening = false;
    // Номер текущей попытки открыть. «Отменить» на прелоадере сдвигает его,
    // и запоздавшая загрузка уже не открывает окно, которое человек отменил.
    let attempt = 0;

    const open = async (source: HTMLImageElement) => {
        if (opening) {
            return;
        }

        opening = true;
        const current = ++attempt;

        // Прелоадер — только если ждать заметно: на быстрой связи и при
        // повторных открытиях он мелькал бы на долю секунды.
        const showPreloader = window.setTimeout(() => preloader.enable(undefined, {
            onCancel: () => {
                attempt++;
                opening = false;
            },
        }), 150);

        try {
            const group = groupOf(source);

            // Картинка в статье ещё не догрузилась — ждём, иначе размеры нулевые.
            await Promise.all([
                loadPswp(),
                ...group.map(img => (img.complete && img.naturalWidth ? null : img.decode().catch(() => null))),
            ]);

            if (current !== attempt) {
                return;
            }

            lightbox.loadAndOpen(group.indexOf(source), group.map(toSlide));
        } catch (error) {
            // Библиотека не скачалась (пропала связь) — картинка просто
            // не раскроется, а следующий тап попробует снова.
            console.error('Окно с картинкой не загрузилось:', error);
            pswpModule = null;
        } finally {
            // Отменённая попытка уже убрала прелоадер и сняла opening — не трогаем:
            // человек мог успеть начать новое открытие.
            if (current === attempt) {
                window.clearTimeout(showPreloader);
                preloader.disable();
                opening = false;
            }
        }
    };

    sources.forEach(source => {
        // С клавиатуры картинка тоже открывается: Tab до неё, Enter или пробел.
        source.tabIndex = 0;
        source.setAttribute('role', 'button');
        source.setAttribute('aria-label', source.alt ? `Раскрыть: ${source.alt}` : 'Раскрыть картинку');

        source.addEventListener('click', () => open(source));

        // Начинаем качать окно заранее: мышь навели или палец коснулся —
        // к моменту клика файл часто уже на месте, и ждать не приходится.
        // Ошибку здесь глотаем: при клике open() попробует скачать снова.
        const prefetch = () => { loadPswp().catch(() => { pswpModule = null; }); };

        source.addEventListener('pointerenter', prefetch, { once: true });
        source.addEventListener('touchstart', prefetch, { once: true, passive: true });
        source.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                open(source);
            }
        });
    });
}
