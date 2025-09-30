const paragraphCount = document.querySelectorAll('p').length;
console.log('Кількість <p>:', paragraphCount);

const h2Count = document.querySelectorAll('h2').length;
console.log('Кількість <h2>:', h2Count);

const bodyBg = getComputedStyle(document.body).backgroundColor;
console.log('background-color <body>:', bodyBg);

const h1 = document.querySelector('h1');

const h1FontSize = h1 ? getComputedStyle(h1).fontSize : 'N/A';
console.log('font-size <h1>:', h1FontSize);

(function addHoverBackgroundToggler() {
    const hoverables = document.querySelectorAll('.hoverable-red');
    hoverables.forEach((element) => {
        element.addEventListener('mouseenter', function () {
            const currentBg = getComputedStyle(this).backgroundColor;
            if (!this.dataset.prevBgColor) {
                this.dataset.prevBgColor = currentBg;
            }
            this.style.backgroundColor = 'red';
        });

        element.addEventListener('mouseleave', function () {
            const prev = this.dataset.prevBgColor || '';
            this.style.backgroundColor = prev;
        });
    });
})();

(function loadImagesWithDelay() {
    const imagesUrl = [
        'images/kv/kv1.jpg',
        'images/kv/kv2.jpg',
        'images/kv/kv3.jpg'
    ]; 

    const enableHeroImages = false;

    if (!enableHeroImages) return;

    window.addEventListener('load', () => {
        setTimeout(() => {
            const parent = document.querySelector('.hero-section');
            if (!parent) return;

            const fragment = document.createDocumentFragment();

            imagesUrl.forEach((url, index) => {
                const img = document.createElement('img');
                img.src = url;
                img.alt = 'Додаткове зображення';
                img.style.maxWidth = '120px';
                img.style.marginRight = '8px';
                img.style.opacity = '0';
                img.style.transition = 'opacity 300ms ease';

                setTimeout(() => {
                    fragment.appendChild(img);
                    parent.appendChild(fragment);
                    requestAnimationFrame(() => {
                        img.style.opacity = '1';
                    });
                }, index * 1000);
            });
        }, 5000);
    });
})(); 
