import Splide from '@splidejs/splide';
import { Options } from '@splidejs/splide';
import {Intersection} from '@splidejs/splide-extension-intersection';
import {AutoplayComponent, Components} from "@splidejs/splide";

const section = document.querySelector('.slider-offer') as HTMLElement | null;

if (section) {
    const splideEl = section.querySelector('.slider-offer__splide') as HTMLElement;

    const options = {
        type: 'loop',
        perPage: 3,
        perMove: 1,
        gap: 21,
        pagination: false,
        arrows: false,
        breakpoints: {
            768: { perPage: 1 },
            1024: { perPage: 2, gap: 30 },
        },
    };

    const observer = new IntersectionObserver((entries, obs) => {
        if (entries[0].isIntersecting) {
            obs.disconnect();

            console.log("assss")

            // Promise.all([
            //     import('@splidejs/splide'),
            // ]).then(([{ default: Splide }]) => {
            //     new Splide(splideEl, options).mount();
            // });
        }
    }, { threshold: 0 });

    observer.observe(section);
}
