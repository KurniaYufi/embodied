import AOS from 'aos';

AOS.init({
    duration: 700,
    easing: 'ease-out-cubic',
    once: true,
    offset: 60,
    disable: () => window.matchMedia('(prefers-reduced-motion: reduce)').matches,
});
