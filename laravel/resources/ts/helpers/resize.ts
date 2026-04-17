/**
 * Глобальный resize-наблюдатель с отслеживанием брейкпоинтов.
 *
 * Зеркалит брейкпоинты из resources/scss/utils/_variables.scss.
 * Слушает только изменение ШИРИНЫ (игнорирует скрытие/появление тулбара на мобилках).
 * Диспатчит два кастомных ивента на window:
 *   - "widthChange"      — при любом изменении ширины (каждый пиксель, с дебаунсом 500мс)
 *   - "breakpointChange" — только при смене брейкпоинта
 *
 * Подписка из любой части проекта:
 *   window.addEventListener("widthChange", (e) => {
 *       console.log(e.detail.width);
 *   });
 *   window.addEventListener("breakpointChange", (e) => {
 *       console.log(e.detail.previous, "→", e.detail.current);
 *   });
 */

import { breakpoint, type Breakpoint } from '@utils';

export type { Breakpoint };
export { breakpoint };

let prevWidth:          number                              = window.innerWidth;
let currentBreakpoint:  Breakpoint                          = breakpoint.current(window.innerWidth);
let resizeTimer:        ReturnType<typeof setTimeout> | null = null;

const onResize = (): void => {
    if (resizeTimer !== null) clearTimeout(resizeTimer);

    resizeTimer = setTimeout(() => {
        const width = window.innerWidth;

        // Реагируем только на изменение ширины — игнорируем скрытие тулбара на мобилках
        if (width === prevWidth) return;
        prevWidth = width;

        window.dispatchEvent(new CustomEvent("widthChange", { detail: { width } }));

        const next = breakpoint.current(width);

        console.log(`[resize] ширина: ${width}px | брейкпоинт: ${next}`);

        if (next === currentBreakpoint) return;

        const previous    = currentBreakpoint;
        currentBreakpoint = next;

        window.dispatchEvent(
            new CustomEvent("breakpointChange", {
                detail: { previous, current: next },
            })
        );
    }, 500);
};

window.addEventListener("resize", onResize);
