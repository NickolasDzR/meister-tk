import { getElementHeight } from "@utils";
import { getBreakpoint, type Breakpoint } from "@helpers/resize";

const postCardTitleBottomMargin: Partial<Record<Breakpoint, number>> = {
    md: 65,
    xs: 30,
};

const BREAKPOINT_ORDER: Breakpoint[] = ["xs", "sm", "md", "lg", "xl", "xxl"];

const getMarginForBreakpoint = (bp: Breakpoint): number => {
    let result = 0;
    for (const b of BREAKPOINT_ORDER) {
        if (postCardTitleBottomMargin[b] !== undefined) {
            result = postCardTitleBottomMargin[b]!;
        }
        if (b === bp) break;
    }
    return result;
};

type CardState = {
    title:         HTMLElement;
    content:       HTMLElement;
    titleHeight:   number; // высота без отступа
    appliedMargin: number; // отступ, уже применённый в transform
};

const cardStates: CardState[] = [];

const applyTransform = (state: CardState): void => {
    state.content.style.transform = `translateY(calc(100% - ${state.titleHeight + state.appliedMargin}px))`;
};

const initPostCards = (): void => {
    const cards  = document.querySelectorAll<HTMLElement>(".post-card");
    const margin = getMarginForBreakpoint(getBreakpoint(window.innerWidth));

    // Следит за изменением высоты заголовка (перенос строк при ресайзе)
    const observer = new ResizeObserver((entries) => {
        for (const entry of entries) {
            const state = cardStates.find((s) => s.title === entry.target);
            if (!state) continue;

            const newHeight = getElementHeight(state.title);
            if (newHeight === state.titleHeight) continue;

            state.titleHeight = newHeight;
            applyTransform(state);
        }
    });

    cards.forEach((card) => {
        const title   = card.querySelector<HTMLElement>(".post-card__title");
        const content = card.querySelector<HTMLElement>(".post-card__content");

        if (!title || !content) return;

        const state: CardState = {
            title,
            content,
            titleHeight:   getElementHeight(title),
            appliedMargin: margin,
        };

        cardStates.push(state);
        applyTransform(state);
        observer.observe(title);
    });
};

window.addEventListener("breakpointChange", (e) => {
    const { current } = (e as CustomEvent<{ previous: Breakpoint; current: Breakpoint }>).detail;
    const newMargin   = getMarginForBreakpoint(current);

    cardStates.forEach((state) => {
        if (state.appliedMargin === newMargin) return;
        state.appliedMargin = newMargin;
        applyTransform(state);
    });
});

document.addEventListener("DOMContentLoaded", initPostCards);
