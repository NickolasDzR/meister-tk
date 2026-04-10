import {DynamicLoad} from "@mornya/dynamic-load-libs";

/**
 * Возвращает высоту элемента.
 *
 * @param element - DOM-элемент
 * @param includeMargin - учитывать margin-top и margin-bottom (по умолчанию false)
 *
 * Без флага: height + padding-top + padding-bottom + border-top + border-bottom (offsetHeight)
 * С флагом:  то же самое + margin-top + margin-bottom
 */
export const getElementHeight = (element: HTMLElement, includeMargin = false): number => {
    if (!element) {
        console.error("getElementHeight: нужно передать DOM-элемент");
        return 0;
    }

    const height = element.offsetHeight; // content + padding + border

    if (!includeMargin) return height;

    const style = getComputedStyle(element);
    const marginTop    = parseFloat(style.marginTop)    || 0;
    const marginBottom = parseFloat(style.marginBottom) || 0;

    return height + marginTop + marginBottom;
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