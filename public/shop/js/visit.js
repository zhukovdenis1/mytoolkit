let triggered = false;

function isProbablyHuman() {
    try {
        // Проверка наличия плагинов
        //if (!navigator.plugins || navigator.plugins.length === 0) return false; //в мобильниках может быть 0

        // Проверка количества логических ядер (0 или 1 — подозрительно)
        if (!navigator.hardwareConcurrency || navigator.hardwareConcurrency < 2) return false;

        // Проверка на поддержку devicePixelRatio
        if (!window.devicePixelRatio || window.devicePixelRatio < 1) return false;

        // Проверка ширины окна (может быть 0 у headless)
        if (!window.innerWidth || window.innerWidth < 200) return false;

        // Проверка webdriver-флага (включён у headless Chrome/Firefox)
        if (navigator.webdriver) return false;

        return true;
    } catch (e) {
        return false; // если ошибка при проверке — считаем, что бот
    }
}

function logVisit() {
    if (triggered) return;
    if (!isProbablyHuman()) return;

    triggered = true;
    $.get('/shop/visit');
}

document.addEventListener('mousemove', logVisit, { once: true });
document.addEventListener('click', logVisit, { once: true });
document.addEventListener('scroll', logVisit, { once: true });

$('._go').on('click', function() {
    let $this = $(this);
    $.get('/shop/visit?goref=' + $this.attr('href'));
});
