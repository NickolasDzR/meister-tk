const sliderEl = document.querySelector('.slider-offer__splide') as HTMLElement | null;

const slidesOverflow = (): boolean => {
    const track = sliderEl?.querySelector('.splide__track') as HTMLElement | null;
    const list  = sliderEl?.querySelector('.splide__list')  as HTMLElement | null;
    if (!track || !list) return false;

    const slides = Array.from(
        list.querySelectorAll('.splide__slide:not(.splide__slide--clone)')
    ) as HTMLElement[];
    if (!slides.length) return false;

    const gap = parseFloat(getComputedStyle(list).gap) ||
                parseFloat(getComputedStyle(slides[0]).marginRight) || 0;
    const totalWidth = slides.reduce((sum, s) => sum + s.offsetWidth, 0) + gap * (slides.length - 1);

    return totalWidth > track.offsetWidth;
};

let splideInstance: import('@splidejs/splide').Splide | null = null;

const loadSlider = async () => {
    const { default: Splide } = await import('@splidejs/splide');

    splideInstance = new Splide(sliderEl!, {
        autoWidth: true,
        gap: 21,
        pagination: false,
        arrows: true,
        drag: 'free',
        autoScroll: false,
        mediaQuery: 'min',
        breakpoints: {
            1200: { gap: 30 },
        },
    });

    const counter = sliderEl!.querySelector('.slider-offer__counter') as HTMLElement | null;

    const updateCounter = (index: number) => {
        if (counter) counter.textContent = `${index + 1} / ${splideInstance!.length}`;
    };

    splideInstance.on('mounted', () => updateCounter(splideInstance!.index));
    splideInstance.on('move', (newIndex: number) => updateCounter(newIndex));

    splideInstance.mount();
};

const destroySlider = () => {
    if (splideInstance) {
        console.log("destroy");
        splideInstance.destroy(true);
        splideInstance = null;

        sliderEl?.classList.remove('is-initialized', 'is-overflow');
    }
};

const handleResize = () => {
    if (slidesOverflow()) {
        if (!splideInstance) loadSlider();
    } else {
        if (splideInstance) destroySlider();
    }
};

if (sliderEl) {
    new IntersectionObserver((entries, observer) => {
        if (entries[0].isIntersecting) {
            observer.disconnect();
            window.addEventListener('widthChange', handleResize);
            handleResize();
        }
    }, { threshold: 0 }).observe(sliderEl);
}
