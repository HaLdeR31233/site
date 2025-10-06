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

(function comparePhrasesWithSet() {
    let previousPhraseSet = null;

    function normalize(text) {
        return text
            .toLowerCase()
            .replace(/[.,!?;:()\[\]{}"'`«»]/g, ' ')
            .split(/\s+/)
            .filter(Boolean);
    }

    function toSet(words) {
        return new Set(words);
    }

    function intersectSets(a, b) {
        const result = [];
        for (const item of a) {
            if (b.has(item)) result.push(item);
        }
        return result;
    }

    window.addEventListener('load', () => {
        const input = document.getElementById('phrase-input');
        const button = document.getElementById('compare-btn');
        const resultBox = document.getElementById('compare-result');
        if (!input || !button || !resultBox) return;

        button.addEventListener('click', () => {
            const words = normalize(input.value);
            const currentSet = toSet(words);

            if (previousPhraseSet) {
                const common = intersectSets(previousPhraseSet, currentSet);
                resultBox.textContent = common.length ? `Спільні слова: ${common.join(', ')}` : 'Спільних слів немає';
            } else {
                resultBox.textContent = 'Збережено першу фразу. Введіть наступну для порівняння.';
            }
            previousPhraseSet = currentSet;
            input.value = '';
            input.focus();
        });
    });
})(); 
