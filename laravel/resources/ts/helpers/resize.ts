/**
 * Глобальный resize-наблюдатель с отслеживанием брейкпоинтов.
 *
 * Зеркалит брейкпоинты из resources/scss/utils/_variables.scss.
 * Слушает только изменение ШИРИНЫ (игнорирует скрытие/появление тулбара на мобилках).
 * Диспатчит кастомный ивент "breakpointChange" на window при смене брейкпоинта.
 *
 * Подписка из любой части проекта:
 *   window.addEventListener("breakpointChange", (e) => {
 *       console.log(e.detail.previous, "→", e.detail.current);
 *   });
 */

export const BREAKPOINTS = {
    sm:  576,
    md:  768,
    lg:  992,
    xl:  1200,
    xxl: 1400,
} as const;

export type Breakpoint = keyof typeof BREAKPOINTS | "xs";

export const getBreakpoint = (width: number): Breakpoint => {
    if (width >= BREAKPOINTS.xxl) return "xxl";
    if (width >= BREAKPOINTS.xl)  return "xl";
    if (width >= BREAKPOINTS.lg)  return "lg";
    if (width >= BREAKPOINTS.md)  return "md";
    if (width >= BREAKPOINTS.sm)  return "sm";
    return "xs";
};

let prevWidth:          number                              = window.innerWidth;
let currentBreakpoint:  Breakpoint                          = getBreakpoint(window.innerWidth);
let resizeTimer:        ReturnType<typeof setTimeout> | null = null;

const onResize = (): void => {
    if (resizeTimer !== null) clearTimeout(resizeTimer);

    resizeTimer = setTimeout(() => {
        const width = window.innerWidth;

        // Реагируем только на изменение ширины — игнорируем скрытие тулбара на мобилках
        if (width === prevWidth) return;
        prevWidth = width;

        const next = getBreakpoint(width);

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
