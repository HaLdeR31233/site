# DIM.RIA Laravel Project

## Опис проєкту

Це Laravel проєкт для системи управління нерухомістю DIM.RIA. Проєкт реалізує повну MVC структуру з моделлю, контролером, шаблонами, фабрикою, міграцією, сидером та маршрутами.

## Структура проєкту

### Створені файли:

1. **Міграція:** `database/migrations/2024_01_01_000000_create_properties_table.php`
   - Створює таблицю `properties` з полями: id, title, description, price, address, rooms, area, type, status, timestamps

2. **Модель:** `app/Models/Property.php`
   - Eloquent модель для роботи з таблицею properties
   - Визначені fillable поля та casts

3. **Фабрика:** `database/factories/PropertyFactory.php`
   - Генерує тестові дані для нерухомості
   - Використовує Faker для створення реалістичних даних

4. **Сидер:** `database/seeders/PropertySeeder.php`
   - Заповнює базу даних 50 записами нерухомості
   - Зареєстрований в `DatabaseSeeder.php`

5. **Контролер:** `app/Http/Controllers/PropertyController.php`
   - Resource контролер з методами: index, create, store, show, edit, update, destroy
   - Валідація вхідних даних
   - Повернення views з даними

6. **Шаблони (Views):**
   - `resources/views/layouts/app.blade.php` - основний layout
   - `resources/views/properties/index.blade.php` - список нерухомості
   - `resources/views/properties/show.blade.php` - деталі нерухомості
   - `resources/views/properties/create.blade.php` - форма створення
   - `resources/views/properties/edit.blade.php` - форма редагування

7. **Маршрути:** `routes/web.php`
   - Resource route для properties
   - Створює всі стандартні CRUD маршрути

## Встановлення та запуск

### 1. Встановлення залежностей

```bash
cd dim-ria-laravel
composer install
```

### 2. Налаштування .env

Скопіюйте `.env.example` в `.env`:

```bash
copy .env.example .env
```

Налаштуйте базу даних в `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dim_ria
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Генерація ключа

```bash
php artisan key:generate
```

### 4. Створення бази даних

Створіть базу даних в MySQL:

```sql
CREATE DATABASE dim_ria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Виконання міграцій

```bash
php artisan migrate
```

### 6. Заповнення бази даних (сидер)

```bash
php artisan db:seed
```

Або конкретний сидер:

```bash
php artisan db:seed --class=PropertySeeder
```

### 7. Запуск сервера

```bash
php artisan serve
```

Проєкт буде доступний за адресою: `http://localhost:8000`

## Доступні маршрути

| Метод | URL | Опис |
|-------|-----|------|
| GET | `/` | Головна сторінка |
| GET | `/properties` | Список нерухомості |
| GET | `/properties/create` | Форма створення |
| POST | `/properties` | Збереження нової нерухомості |
| GET | `/properties/{id}` | Деталі нерухомості |
| GET | `/properties/{id}/edit` | Форма редагування |
| PUT | `/properties/{id}` | Оновлення нерухомості |
| DELETE | `/properties/{id}` | Видалення нерухомості |

Перегляд всіх маршрутів:

```bash
php artisan route:list
```

## Використані технології

- **Laravel 12** - PHP фреймворк
- **MySQL** - база даних
- **Bootstrap 5** - CSS фреймворк
- **Blade** - шаблонізатор
- **Eloquent ORM** - ORM для роботи з БД
- **Faker** - генерація тестових даних

## Структура файлів

```
dim-ria-laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── PropertyController.php
│   └── Models/
│       └── Property.php
├── database/
│   ├── factories/
│   │   └── PropertyFactory.php
│   ├── migrations/
│   │   └── 2024_01_01_000000_create_properties_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── PropertySeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       └── properties/
│           ├── index.blade.php
│           ├── show.blade.php
│           ├── create.blade.php
│           └── edit.blade.php
└── routes/
    └── web.php
```

## Визначення термінів

### Міграція (Migration)
Файл, який описує зміни в структурі бази даних. Дозволяє версіонувати схему БД та легко відкочувати зміни.

### Модель (Model)
Клас, який представляє таблицю в базі даних. Надає інтерфейс для роботи з даними через Eloquent ORM.

### Контролер (Controller)
Клас, який обробляє HTTP-запити та повертає відповіді. Виступає посередником між моделлю та представленням.

### Шаблон (View)
Файл, який містить HTML-розмітку та відображає дані користувачу. Використовує Blade шаблонізатор.

### Фабрика (Factory)
Клас для генерації тестових даних. Використовує бібліотеку Faker для створення реалістичних фейкових даних.

### Сидер (Seeder)
Клас для заповнення бази даних початковими даними. Використовує фабрики або безпосередньо створює записи в БД.

### Маршрут (Route)
Зв'язок між URL-адресою та методом контролера. Визначає, який код виконається при запиті до певного URL.

## Автор

**Розробник:** Єгор  
**Група:** КН-24  
**Університет:** Ельворті

---

*Проєкт створено: 2024*
