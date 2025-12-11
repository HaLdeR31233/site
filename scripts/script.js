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

// Axios - заміна fetch для завантаження зображень
(function loadImageWithAxios() {
    async function fetchRandomImage() {
        const resultBox = document.getElementById('dog-result');
        if (!resultBox) return;
        resultBox.textContent = 'Завантаження...';
        
        try {
            const response = await axios.get('https://picsum.photos/300/200?random=' + Date.now(), {
                timeout: 5000,
                responseType: 'blob'
            });
            
            const img = document.createElement('img');
            img.src = URL.createObjectURL(response.data);
            img.alt = 'Випадкове зображення';
            img.style.maxWidth = '260px';
            img.style.borderRadius = '8px';
            img.style.display = 'block';
            img.style.marginTop = '6px';
            img.style.border = '2px solid #ddd';
            resultBox.innerHTML = '';
            resultBox.appendChild(img);
            
        } catch (error) {
            console.error('Axios Error:', error);
            resultBox.textContent = 'Помилка завантаження: ' + (error.message || 'Невідома помилка');
        }
    }

    window.addEventListener('load', () => {
        const btn = document.getElementById('dog-btn');
        if (btn) {
            btn.addEventListener('click', fetchRandomImage);
        }
    });
})();

// Vue.js - реактивний пошук
(function initVueSearch() {
    window.addEventListener('load', () => {
        const searchField = document.querySelector('.search-field');
        if (!searchField) return;

        // Vue.js для реактивного пошуку з використанням Lodash
        const { createApp } = Vue;
        const searchApp = createApp({
            data() {
                return {
                    searchQuery: ''
                };
            },
            watch: {
                searchQuery(newVal) {
                    const properties = Array.from(document.querySelectorAll('.property-card'));
                    const query = _.toLower(newVal);
                    
                    if (!query) {
                        properties.forEach(card => card.style.display = '');
                        return;
                    }

                    const filtered = _.filter(properties, card => {
                        const text = _.toLower(card.textContent);
                        return _.includes(text, query);
                    });

                    properties.forEach(card => {
                        card.style.display = _.includes(filtered, card) ? '' : 'none';
                    });
                }
            }
        });

        const vueContainer = document.createElement('div');
        vueContainer.id = 'vue-search';
        vueContainer.style.display = 'none';
        document.body.appendChild(vueContainer);
        searchApp.mount('#vue-search');

        searchField.addEventListener('input', function() {
            if (searchApp._instance) {
                searchApp._instance.data.searchQuery = this.value;
            }
        });
    });
})();

// Chart.js - статистика нерухомості
(function initPropertyChart() {
    window.addEventListener('load', () => {
        const chartContainer = document.querySelector('.properties');
        if (!chartContainer) return;

        const statsSection = document.createElement('section');
        statsSection.className = 'statistics-section';
        statsSection.innerHTML = `
            <h2>Статистика нерухомості</h2>
            <div style="max-width: 400px; margin: 20px auto;">
                <canvas id="propertyStatsChart"></canvas>
            </div>
        `;
        chartContainer.parentNode.insertBefore(statsSection, chartContainer);

        const ctx = document.getElementById('propertyStatsChart');
        if (!ctx) return;

        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Квартири', 'Будинки', 'Офіси'],
                datasets: [{
                    data: [12, 5, 3],
                    backgroundColor: ['#667eea', '#764ba2', '#f093fb']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
})();

// jQuery - спрощені DOM маніпуляції та анімації
(function initJQueryFeatures() {
    $(document).ready(function() {
        // Анімація при наведенні на картки
        $('.property-card').hover(
            function() {
                $(this).fadeTo(200, 0.9);
            },
            function() {
                $(this).fadeTo(200, 1);
            }
        );

        // Плавне прокручування до секцій
        $('.nav-links a').on('click', function(e) {
            const href = $(this).attr('href');
            if (href.startsWith('#')) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $(href).offset().top
                }, 500);
            }
        });
    });
})();

// Lodash - обробка даних для порівняння фраз
(function enhanceCompareWithLodash() {
    window.addEventListener('load', () => {
        const input = document.getElementById('phrase-input');
        const button = document.getElementById('compare-btn');
        const resultBox = document.getElementById('compare-result');
        if (!input || !button || !resultBox) return;

        let previousPhraseWords = null;

        button.addEventListener('click', () => {
            const words = _.words(_.toLower(input.value));
            
            if (previousPhraseWords) {
                const common = _.intersection(previousPhraseWords, words);
                const unique = _.difference(words, previousPhraseWords);
                
                if (common.length) {
                    resultBox.textContent = `Спільні слова: ${common.join(', ')}`;
                } else if (unique.length) {
                    resultBox.textContent = `Нові слова: ${unique.join(', ')}`;
                } else {
                    resultBox.textContent = 'Спільних слів немає';
                }
            } else {
                resultBox.textContent = 'Збережено першу фразу. Введіть наступну для порівняння.';
            }
            
            previousPhraseWords = words;
            input.value = '';
            input.focus();
        });
    });
})();

// Moment.js - форматування дат у картках нерухомості
(function addDatesWithMoment() {
    window.addEventListener('load', () => {
        const propertyCards = document.querySelectorAll('.property-card');
        const today = moment();
        
        propertyCards.forEach((card, index) => {
            const infoDiv = card.querySelector('.property-info');
            if (!infoDiv) return;

            // Додаємо дату публікації (симуляція)
            const publishedDate = moment().subtract(index + 1, 'days');
            const dateElement = document.createElement('p');
            dateElement.className = 'published-date';
            dateElement.style.fontSize = '0.9em';
            dateElement.style.color = '#666';
            dateElement.style.marginTop = '8px';
            dateElement.textContent = `Опубліковано: ${publishedDate.format('DD MMMM YYYY')} (${publishedDate.fromNow()})`;
            
            const details = infoDiv.querySelector('.details');
            if (details) {
                details.parentNode.insertBefore(dateElement, details.nextSibling);
            }
        });
    });
})();
