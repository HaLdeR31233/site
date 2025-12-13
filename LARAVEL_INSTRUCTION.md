# Інструкція по створенню та налаштуванню проєкту Laravel

## DIM.RIA - Система управління нерухомістю на Laravel

**Версія:** 1.0.0  
**Дата створення:** 2024

---

## 📋 Зміст

1. [Встановлення Laravel](#встановлення-laravel)
2. [Початкове налаштування](#початкове-налаштування)
3. [Реалізація MVC](#реалізація-mvc)
4. [Визначення термінів](#визначення-термінів)
5. [Структура проєкту](#структура-проєкту)

---

## Встановлення Laravel

### Крок 1: Перевірка вимог

Перед встановленням переконайтеся, що у вас встановлено:

- **PHP** >= 8.1
- **Composer** (менеджер залежностей PHP)
- **MySQL** або інша база даних
- **Розширення PHP:**
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

Перевірка версії PHP:
```bash
php -v
```

Перевірка Composer:
```bash
composer --version
```

### Крок 2: Встановлення Laravel через Composer

Відкрийте термінал у бажаній директорії та виконайте:

```bash
composer create-project laravel/laravel dim-ria-laravel
```

Або встановіть Laravel глобально:
```bash
composer global require laravel/installer
laravel new dim-ria-laravel
```

### Крок 3: Перехід у директорію проєкту

```bash
cd dim-ria-laravel
```

---

## Початкове налаштування

### Крок 1: Налаштування файлу .env

Скопіюйте файл `.env.example` у `.env`:

```bash
copy .env.example .env
```

Або на Linux/Mac:
```bash
cp .env.example .env
```

### Крок 2: Генерація ключа додатку

```bash
php artisan key:generate
```

Ця команда генерує унікальний `APP_KEY` для шифрування даних.

### Крок 3: Налаштування бази даних

Відкрийте файл `.env` та налаштуйте підключення до БД:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dim_ria
DB_USERNAME=root
DB_PASSWORD=
```

### Крок 4: Створення бази даних

Створіть базу даних у MySQL:

```sql
CREATE DATABASE dim_ria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Крок 5: Запуск міграцій (якщо є)

```bash
php artisan migrate
```

### Крок 6: Запуск сервера розробки

```bash
php artisan serve
```

Сервер буде доступний за адресою: `http://localhost:8000`

---

## Реалізація MVC

### Крок 1: Створення міграції (Migration)

**Міграція** - це файл, який описує структуру таблиці в базі даних.

Створення міграції для таблиці `properties`:

```bash
php artisan make:migration create_properties_table
```

Файл буде створено в `database/migrations/YYYY_MM_DD_HHMMSS_create_properties_table.php`

**Приклад міграції:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('address');
            $table->integer('rooms');
            $table->decimal('area', 8, 2);
            $table->string('type'); // apartment, house, commercial
            $table->string('status')->default('active'); // active, sold, rented
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
```

Виконання міграції:
```bash
php artisan migrate
```

---

### Крок 2: Створення моделі (Model)

**Модель** - це клас, який представляє таблицю в базі даних та дозволяє працювати з даними.

Створення моделі:

```bash
php artisan make:model Property
```

Файл буде створено в `app/Models/Property.php`

**Приклад моделі:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'address',
        'rooms',
        'area',
        'type',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'rooms' => 'integer',
    ];
}
```

---

### Крок 3: Створення фабрики (Factory)

**Фабрика** - це клас для генерації тестових даних (seed data).

Створення фабрики:

```bash
php artisan make:factory PropertyFactory
```

Файл буде створено в `database/factories/PropertyFactory.php`

**Приклад фабрики:**

```php
<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $types = ['apartment', 'house', 'commercial'];
        $statuses = ['active', 'sold', 'rented'];
        
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 500000, 5000000),
            'address' => $this->faker->address(),
            'rooms' => $this->faker->numberBetween(1, 5),
            'area' => $this->faker->randomFloat(2, 30, 200),
            'type' => $this->faker->randomElement($types),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}
```

---

### Крок 4: Створення сидера (Seeder)

**Сидер** - це клас для заповнення бази даних початковими даними.

Створення сидера:

```bash
php artisan make:seeder PropertySeeder
```

Файл буде створено в `database/seeders/PropertySeeder.php`

**Приклад сидера:**

```php
<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        Property::factory()->count(50)->create();
    }
}
```

Реєстрація сидера в `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        PropertySeeder::class,
    ]);
}
```

Виконання сидера:
```bash
php artisan db:seed
```

Або конкретного сидера:
```bash
php artisan db:seed --class=PropertySeeder
```

---

### Крок 5: Створення контролера (Controller)

**Контролер** - це клас, який обробляє HTTP-запити та повертає відповіді.

Створення контролера:

```bash
php artisan make:controller PropertyController --resource
```

Флаг `--resource` створює стандартні методи CRUD.

Файл буде створено в `app/Http/Controllers/PropertyController.php`

**Приклад контролера:**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::where('status', 'active')->paginate(12);
        return view('properties.index', compact('properties'));
    }

    public function show(Property $property)
    {
        return view('properties.show', compact('property'));
    }

    public function create()
    {
        return view('properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'rooms' => 'required|integer|min:1',
            'area' => 'required|numeric|min:0',
            'type' => 'required|in:apartment,house,commercial',
        ]);

        Property::create($validated);

        return redirect()->route('properties.index')
            ->with('success', 'Нерухомість успішно додана!');
    }
}
```

---

### Крок 6: Створення шаблонів (Views)

**Шаблон (View)** - це файл, який відображає HTML-розмітку для користувача.

Створення директорії для шаблонів:

```bash
mkdir resources/views/properties
```

**Приклад шаблону `resources/views/properties/index.blade.php`:**

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Каталог нерухомості</h1>
    
    <div class="row">
        @foreach($properties as $property)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $property->title }}</h5>
                        <p class="card-text">{{ $property->description }}</p>
                        <p class="card-text">
                            <strong>Ціна:</strong> {{ number_format($property->price, 2) }} грн
                        </p>
                        <p class="card-text">
                            <strong>Адреса:</strong> {{ $property->address }}
                        </p>
                        <p class="card-text">
                            <strong>Кімнат:</strong> {{ $property->rooms }} | 
                            <strong>Площа:</strong> {{ $property->area }} м²
                        </p>
                        <a href="{{ route('properties.show', $property) }}" class="btn btn-primary">
                            Детальніше
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{ $properties->links() }}
</div>
@endsection
```

**Приклад шаблону `resources/views/properties/show.blade.php`:**

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $property->title }}</h1>
    
    <div class="card">
        <div class="card-body">
            <p><strong>Опис:</strong> {{ $property->description }}</p>
            <p><strong>Ціна:</strong> {{ number_format($property->price, 2) }} грн</p>
            <p><strong>Адреса:</strong> {{ $property->address }}</p>
            <p><strong>Кімнат:</strong> {{ $property->rooms }}</p>
            <p><strong>Площа:</strong> {{ $property->area }} м²</p>
            <p><strong>Тип:</strong> {{ $property->type }}</p>
            <p><strong>Статус:</strong> {{ $property->status }}</p>
        </div>
    </div>
    
    <a href="{{ route('properties.index') }}" class="btn btn-secondary mt-3">
        Назад до списку
    </a>
</div>
@endsection
```

---

### Крок 7: Створення маршрутів (Routes)

**Маршрут (Route)** - це зв'язок між URL-адресою та методом контролера.

Відкрийте файл `routes/web.php` та додайте маршрути:

```php
<?php

use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('properties', PropertyController::class);
```

Це створить наступні маршрути:

| Метод | URL | Дія | Метод контролера |
|-------|-----|-----|------------------|
| GET | `/properties` | Список нерухомості | `index()` |
| GET | `/properties/create` | Форма створення | `create()` |
| POST | `/properties` | Збереження | `store()` |
| GET | `/properties/{id}` | Деталі | `show()` |
| GET | `/properties/{id}/edit` | Форма редагування | `edit()` |
| PUT/PATCH | `/properties/{id}` | Оновлення | `update()` |
| DELETE | `/properties/{id}` | Видалення | `destroy()` |

Перегляд всіх маршрутів:
```bash
php artisan route:list
```

---

## Визначення термінів

### 1. Міграція (Migration)

**Міграція** - це файл, який описує зміни в структурі бази даних. Вона дозволяє версіонувати схему БД та легко відкочувати зміни. Міграції зберігаються в директорії `database/migrations/`.

**Переваги:**
- Версіонування структури БД
- Легке відкочування змін
- Спільна робота в команді
- Контроль історії змін

### 2. Модель (Model)

**Модель** - це клас, який представляє таблицю в базі даних. Вона надає інтерфейс для роботи з даними через Eloquent ORM. Моделі зберігаються в `app/Models/`.

**Функції:**
- Взаємодія з БД
- Валідація даних
- Відносини між таблицями
- Бізнес-логіка

### 3. Контролер (Controller)

**Контролер** - це клас, який обробляє HTTP-запити від користувача та повертає відповіді. Він виступає посередником між моделлю та представленням. Контролери зберігаються в `app/Http/Controllers/`.

**Функції:**
- Обробка HTTP-запитів
- Валідація вхідних даних
- Виклик методів моделі
- Повернення відповідей (views, JSON, redirects)

### 4. Шаблон (View)

**Шаблон (View)** - це файл, який містить HTML-розмітку та відображає дані користувачу. У Laravel використовується Blade - потужний шаблонізатор. Шаблони зберігаються в `resources/views/`.

**Можливості Blade:**
- Наслідування шаблонів
- Вставка змінних
- Цикли та умови
- Компоненти та секції

### 5. Фабрика (Factory)

**Фабрика** - це клас для генерації тестових даних. Вона використовує бібліотеку Faker для створення реалістичних фейкових даних. Фабрики зберігаються в `database/factories/`.

**Призначення:**
- Генерація тестових даних
- Заповнення БД для тестування
- Створення seed data
- Тестування додатку

### 6. Сидер (Seeder)

**Сидер** - це клас для заповнення бази даних початковими даними. Він використовує фабрики або безпосередньо створює записи в БД. Сидери зберігаються в `database/seeders/`.

**Призначення:**
- Заповнення БД початковими даними
- Створення тестових користувачів
- Ініціалізація довідників
- Підготовка даних для розробки

### 7. Маршрут (Route)

**Маршрут** - це зв'язок між URL-адресою та методом контролера або замиканням (closure). Маршрути визначають, який код виконається при запиті до певного URL. Маршрути зберігаються в `routes/web.php` (веб) або `routes/api.php` (API).

**Типи маршрутів:**
- GET - отримання даних
- POST - створення даних
- PUT/PATCH - оновлення даних
- DELETE - видалення даних

---

## Структура проєкту

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
│   │   └── YYYY_MM_DD_HHMMSS_create_properties_table.php
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
│           └── create.blade.php
├── routes/
│   └── web.php
├── .env
└── composer.json
```

---

## Команди Artisan для роботи з MVC

### Міграції

```bash
# Створити міграцію
php artisan make:migration create_table_name

# Виконати міграції
php artisan migrate

# Відкотити останню міграцію
php artisan migrate:rollback

# Відкотити всі міграції
php artisan migrate:reset

# Перезапустити міграції
php artisan migrate:fresh
```

### Моделі

```bash
# Створити модель
php artisan make:model ModelName

# Створити модель з міграцією
php artisan make:model ModelName -m

# Створити модель з міграцією та контролером
php artisan make:model ModelName -mcr
```

### Контролери

```bash
# Створити контролер
php artisan make:controller ControllerName

# Створити resource контролер
php artisan make:controller ControllerName --resource

# Створити API контролер
php artisan make:controller ControllerName --api
```

### Фабрики та Сидери

```bash
# Створити фабрику
php artisan make:factory FactoryName

# Створити сидер
php artisan make:seeder SeederName

# Виконати сидери
php artisan db:seed

# Виконати конкретний сидер
php artisan db:seed --class=SeederName
```

### Маршрути

```bash
# Перегляд всіх маршрутів
php artisan route:list

# Перегляд маршрутів для конкретного контролера
php artisan route:list --controller=PropertyController
```

---

## Перевірка роботи проєкту

1. Запустіть сервер:
```bash
php artisan serve
```

2. Відкрийте браузер:
```
http://localhost:8000/properties
```

3. Перевірте, що:
   - Відображається список нерухомості
   - Дані завантажуються з БД
   - Працює пагінація
   - Можна переглянути деталі об'єкта

---

## Додаткові ресурси

- **Офіційна документація Laravel:** [https://laravel.com/docs](https://laravel.com/docs)
- **Eloquent ORM:** [https://laravel.com/docs/eloquent](https://laravel.com/docs/eloquent)
- **Blade Templates:** [https://laravel.com/docs/blade](https://laravel.com/docs/blade)
- **Routing:** [https://laravel.com/docs/routing](https://laravel.com/docs/routing)
- **Migrations:** [https://laravel.com/docs/migrations](https://laravel.com/docs/migrations)

---

## Контакти

**Проєкт:** DIM.RIA Laravel  
**Розробник:** Єгор  
**Група:** КН-24  
**Університет:** Ельворті

---

*Інструкція створена: 2024*
