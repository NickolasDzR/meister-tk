/**
 * Каталог стилей: числа рядом с образцом.
 *
 * Снимаем их с самого элемента, а не подписываем руками. Подпись, написанная
 * в разметке, через месяц разойдётся с кодом и будет врать; прочитанная
 * у браузера — не может.
 */

type Metric = [label: string, value: string];

function describe(el: HTMLElement): Metric[] {
    const style = getComputedStyle(el);
    const size = parseFloat(style.fontSize);
    const leading = parseFloat(style.lineHeight);

    // line-height: normal числом не отдаётся — показываем как есть.
    const ratio = Number.isFinite(leading) && size
        ? `${round(leading / size)} · ${round(leading)}px`
        : style.lineHeight;

    return [
        ['размер', `${round(size)}px`],
        ['вес', style.fontWeight],
        ['межстрочный', ratio],
    ];
}

function round(value: number): string {
    return String(Math.round(value * 100) / 100);
}

/**
 * Размеры коробки — для кнопок. Шрифт о них ничего не говорит, а сверяем
 * мы с макетом именно их.
 */
function describeBox(el: HTMLElement): Metric[] {
    const style = getComputedStyle(el);
    const border = parseFloat(style.borderTopWidth);

    return [
        ['высота', `${round(el.getBoundingClientRect().height)}px`],
        ['отступы', `${round(parseFloat(style.paddingTop))} / ${round(parseFloat(style.paddingLeft))}`],
        ['радиус', style.borderTopLeftRadius],
        ['рамка', border ? `${round(border)}px` : 'нет'],
    ];
}

function fill(list: Element, metrics: Metric[]): void {
    list.textContent = '';

    metrics.forEach(([label, value]) => {
        const dt = document.createElement('dt');
        dt.textContent = label;

        const dd = document.createElement('dd');
        dd.textContent = value;

        list.append(dt, dd);
    });
}

function paint(): void {
    document.querySelectorAll<HTMLElement>('[data-sample]').forEach((sample) => {
        // Класс висит на внутреннем элементе, обёртка нужна только для фона.
        const target = sample.firstElementChild as HTMLElement | null;
        const list = sample.closest('.sg__row')?.querySelector('.sg__metrics');

        if (!target || !list) {
            return;
        }

        fill(list, describe(target));

        const box = sample.closest('.sg__row')?.querySelector('.sg__metrics_box');

        if (box) {
            fill(box, describeBox(target));
        }
    });
}

paint();

// Размеры меняются на брейкпоинтах — пересчитываем, когда меняют ширину окна.
let timer: number | undefined;
window.addEventListener('resize', () => {
    window.clearTimeout(timer);
    timer = window.setTimeout(paint, 150);
});

// Шрифт приезжает позже разметки: пока он грузится, браузер считает
// межстрочный по запасному начертанию, и число будет чужим.
document.fonts?.ready.then(paint);
