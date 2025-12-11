KN24 PHP Project

Автор: Супрун Єгор
Група: КН-24
Університет: Ельворті

🚀 Технології
- PHP 8+ - основна мова програмування
- Latte - сучасний шаблонізатор для PHP
- SimpleRouter - легкий маршрутизатор
- Composer - менеджер залежностей

📁 Структура проекту
```
site/
├── 📁 public/
│   ├── 📄 index.php
│   ├── 📁 scripts/
│   │   └── 📄 scriptAsync.js
│   └── 📁 styles/
│       └── 📄 style.css
│
├── 📁 src/
│   ├── 📁 classes/
│   │   ├── 📄 AuthController.php
│   │   ├── 📄 Database.php
│   │   ├── 📄 ErrorController.php
│   │   ├── 📄 HomePageController.php
│   │   ├── 📄 LoginController.php
│   │   ├── 📄 Property.php
│   │   ├── 📄 PropertyController.php
│   │   ├── 📄 RegisterController.php
│   │   ├── 📄 Security.php
│   │   ├── 📄 User.php
│   │   └── 📄 Viewer.php
│   │
│   └── 📁 templates/
│       ├── 📄 @layout.latte
│       ├── 📄 _footer.latte
│       ├── 📄 _nav.latte
│       ├── 📄 error403.latte
│       ├── 📄 error404.latte
│       ├── 📄 error500.latte
│       ├── 📄 home.latte
│       ├── 📄 login.latte
│       ├── 📄 register.latte
│       └── 📁 properties/
│           ├── 📄 create.latte
│           ├── 📄 edit.latte
│           ├── 📄 index.latte
│           └── 📄 show.latte
```

🎨 Особливості дизайну
Сучасний CSS:
- CSS змінні для кольорів, тіней, радіусів
- Градієнтні фони та кнопки
- Анімації та hover-ефекти
- Темна тема з підтримкою data-theme="dark"
- Адаптивний дизайн для мобільних пристроїв
Компоненти:
- Навігація з backdrop blur ефектом
- Форми з градієнтними рамками
- Кнопки з анімаціями блискання
- Картки з тінями та hover-ефектами
- Футер завжди внизу сторінки

🔐 Безпека
Авторизація:
- CSRF захист для всіх форм
- Безпечний логаут з токенами
- Сесійна авторизація з перевіркою
- Підтвердження виходу через JavaScript
Тестові облікові записи:
- Логін: admin, Пароль: 123
- Логін: user,  Пароль: pass

🛠️ Функціональність
Маршрути:
- GET / — Головна сторінка
- GET /login — Форма входу
- POST /login — Обробка авторизації
- GET /logout — Безпечний вихід
Особливості:
- Показ/приховування пароля з емодзі кнопкою
- Асинхронне додавання зображень через JavaScript
- Обробка помилок з красивими сторінками
- Автоматичне генерування CSRF токенів

🎯 Архітектура
MVC Pattern:
- Model — логіка даних
- View — Latte шаблони
- Controller — PHP класи в src/classes/
Компоненти:
- Viewer — рендеринг шаблонів
- AuthController — авторизація та безпека
- ErrorController — обробка помилок
- PropertyController, HomePageController, LoginController — логіка сторінок

📱 Адаптивність
- Mobile First підхід
- Breakpoints: 768px, 480px
- Flexbox та Grid для макетів
- Touch-friendly елементи

📄 Ліцензія
- Проект розроблений для навчальних цілей.


