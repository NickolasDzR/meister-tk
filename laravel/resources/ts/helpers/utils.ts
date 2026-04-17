import {DynamicLoad} from "@mornya/dynamic-load-libs";

export type Breakpoint = 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'xxl';

export const breakpoint = {
    xs:  0,
    sm:  576,
    md:  768,
    lg:  992,
    xl:  1200,
    xxl: 1400,
    order: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'] as Breakpoint[],
    current(width: number): Breakpoint {
        if (width >= this.xxl) return 'xxl';
        if (width >= this.xl)  return 'xl';
        if (width >= this.lg)  return 'lg';
        if (width >= this.md)  return 'md';
        if (width >= this.sm)  return 'sm';
        return 'xs';
    },
};

type SpacingProp =
    | 'margin'   | 'padding'
    | 'margin-top'    | 'margin-right'    | 'margin-bottom'    | 'margin-left'
    | 'padding-top'   | 'padding-right'   | 'padding-bottom'   | 'padding-left';

const SPACING_MAP: Record<SpacingProp, (keyof CSSStyleDeclaration)[]> = {
    'margin':          ['marginTop', 'marginRight', 'marginBottom', 'marginLeft'],
    'padding':         ['paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft'],
    'margin-top':      ['marginTop'],
    'margin-right':    ['marginRight'],
    'margin-bottom':   ['marginBottom'],
    'margin-left':     ['marginLeft'],
    'padding-top':     ['paddingTop'],
    'padding-right':   ['paddingRight'],
    'padding-bottom':  ['paddingBottom'],
    'padding-left':    ['paddingLeft'],
};

const sumSpacing = (style: CSSStyleDeclaration, include: SpacingProp[]): number => {
    const seen = new Set<keyof CSSStyleDeclaration>();
    let total = 0;

    for (const key of include) {
        for (const prop of SPACING_MAP[key] ?? []) {
            if (seen.has(prop)) continue;
            seen.add(prop);
            total += parseFloat(style[prop] as string) || 0;
        }
    }

    return total;
};

/**
 * Возвращает высоту элемента (offsetHeight = content + padding + border).
 *
 * @param element - DOM-элемент
 * @param include - список дополнительных свойств для суммирования
 *
 * @example
 * getElementHeight(el, ['margin'])               // + margin-top + margin-bottom + ...
 * getElementHeight(el, ['margin-top', 'padding-bottom'])
 */
export const getElementHeight = (element: HTMLElement, include: SpacingProp[] = []): number => {
    if (!element) {
        console.error("getElementHeight: нужно передать DOM-элемент");
        return 0;
    }

    const height = element.offsetHeight;

    if (!include.length) return height;

    return height + sumSpacing(getComputedStyle(element), include);
};

/**
 * Возвращает ширину элемента (offsetWidth = content + padding + border).
 *
 * @param element - DOM-элемент
 * @param include - список дополнительных свойств для суммирования
 *
 * @example
 * getElementWidth(el, ['margin'])                // + margin-left + margin-right + ...
 * getElementWidth(el, ['margin-left', 'padding-right'])
 */
export const getElementWidth = (element: HTMLElement, include: SpacingProp[] = []): number => {
    if (!element) {
        console.error("getElementWidth: нужно передать DOM-элемент");
        return 0;
    }

    const width = element.offsetWidth;

    if (!include.length) return width;

    return width + sumSpacing(getComputedStyle(element), include);
};

export const capitalizeFirstLetter = (val: string) => {
    return String(val).charAt(0).toUpperCase() + String(val).slice(1);
}

export const download = {
    async script(path: string, id: string): Promise<any> {
        if (!path || !id) return false;

        return await DynamicLoad.script({
            id: id,
            src: path
        });
    },
}

/**
 * Загружаем YMAP
 */
export const YMAPLoader = async (apikey: string): Promise<any> => {
    if (!apikey) {
        console.error("Нужно передать API key яндекс карт")
    }

    const currentUlr = `https://api-maps.yandex.ru/2.1/?apikey=${apikey}&lang=ru_RU`;

    return await download.script(currentUlr, "ymapInstance")
}

/**
 * Убираем пустые значения в объектах массива
 * @param array
 * @param key
 */
export const removeEmptyValues = (array: Record<string, any>[], key: string) =>
    array.filter(object => object[key] !== null);
/**
 *
 * @param form
 */
export const inputEventHandler = (form: HTMLElement) => {
    if (form) {

    } else {
        console.error("Нужно передать форму с инпутами")
    }
}

export const buttonActiveHandler = {
    disable: (button: HTMLButtonElement) => {
        if (button) {
            button.disabled = true;
        } else {
            console.error("Нужно передать кнопку <button></button> в обработчик")
        }
    },
    enable: (button: HTMLButtonElement) => {
        if (button) {
            button.disabled = false;
        } else {
            console.error("Нужно передать кнопку <button></button> в обработчик")
        }
    }
}

// TODO Сделать нормальный валидатор, чтобы валидировался отсюда, а не из кода. И не просто класс наставлял
export const inputErrorHandler = (input: HTMLInputElement | HTMLDivElement) => {
    if (input) {
        input.classList.add("error");

        preloader.disable();
    } else {
        console.error("Для обработки инпута на ошибки в функцию нужно передать input")
    }
};

/**
 *
 * @param element - элемент в котором нужно вывести прелоадер (показать загрузку)
 */
export const preloader = {
    enable: (preloaderText?: string) => {
        const preloader = document.createElement("DIV");
        preloader.classList.add("preloader");

        const preloaderWrapper = document.createElement("DIV");
        preloaderWrapper.classList.add("preloader__wrapper");

        if (preloaderText) {
            preloaderWrapper.dataset.preloaderText = `${preloaderText}`;
        } else {
            preloaderWrapper.dataset.preloaderText = `Идёт загрузка`;
        }

        preloader.appendChild(preloaderWrapper);

        document.body.appendChild(preloader);

        setTimeout(() => {
            preloader.classList.add("active");
        }, 10)
    },
    disable: () => {
        const preloader = document.querySelector(".preloader");

        if (preloader) {
            preloader.addEventListener("transitionend", () => {
                setTimeout(() => {
                    preloader.remove();
                }, 100)
            }, {once: true});

            preloader.classList.remove("active");
        } else {
            console.error("Прелоадер не найден")
        }
    },
}