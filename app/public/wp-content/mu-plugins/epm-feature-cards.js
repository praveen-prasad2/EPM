/* Equalize related cards even when Elementor stores their rows separately. */
(() => {
    const root = document.querySelector('body:not(.home) .elementor[data-elementor-type="wp-page"]');
    if (!root) return;
    // Balance each card grid without changing Elementor's saved content.
    root.querySelectorAll('.epm-feature-grid').forEach(grid => {
        const cards = Array.from(grid.querySelectorAll('.epm-feature-card'))
            .filter(card => card.closest('.epm-feature-grid') === grid);
        if (cards.length === 6) grid.style.setProperty('--epm-card-columns', '3');
        if (cards.length === 7) grid.style.setProperty('--epm-card-columns', '4');
    });
    const groups = new Map();
    root.querySelectorAll('.epm-feature-card').forEach(card => {
        const key = Array.from(card.classList).find(name => name.startsWith('epm-card-set-'));
        if (!key) return;
        if (!groups.has(key)) groups.set(key, []);
        groups.get(key).push(card);
    });
    let frame;
    function equalize() {
        frame = null;
        const visibleGroups = Array.from(groups.values()).map(cards => cards.filter(card => card.getClientRects().length));
        visibleGroups.flat().forEach(card => card.style.setProperty('--epm-card-height', '0px'));
        // Measure after all resets, then write all results to avoid repeated layout work.
        const sizes = visibleGroups.map(cards => Math.ceil(Math.max(0, ...cards.map(card => card.offsetHeight))));
        visibleGroups.forEach((cards, i) => cards.forEach(card => card.style.setProperty('--epm-card-height', `${sizes[i]}px`)));
    }
    function schedule() { if (!frame) frame = requestAnimationFrame(equalize); }
    window.addEventListener('resize', schedule, { passive: true });
    window.addEventListener('load', schedule, { once: true });
    if (document.fonts) document.fonts.ready.then(schedule);
    // Recalculate for responsive containers and cards revealed in tabs.
    if ('ResizeObserver' in window) {
        const widths = new WeakMap();
        const observer = new ResizeObserver(entries => {
            if (entries.some(entry => {
                const previous = widths.get(entry.target);
                widths.set(entry.target, entry.contentRect.width);
                return previous !== entry.contentRect.width;
            })) schedule();
        });
        root.querySelectorAll('.epm-feature-grid').forEach(grid => observer.observe(grid));
    }
    schedule();
})();
