<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Внутрішня помилка сервера</title>
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .error-container {
            text-align: center;
            padding: 80px 20px;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #e74c3c;
            margin: 0;
        }
        .error-message {
            font-size: 24px;
            margin: 20px 0;
            color: #333;
        }
        .error-link {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .error-link:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="/">DIM.RIA</a>
            </div>
            <ul class="nav-links">
                <li><a href="/">Головна</a></li>
                <li><a href="/login">Увійти</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="error-container">
            <h1 class="error-code">500</h1>
            <p class="error-message">Внутрішня помилка сервера</p>
            <p>Вибачте, але на сервері виникла помилка. Спробуйте пізніше.</p>
            <a href="/" class="error-link">Повернутися на головну</a>
        </div>
    </main>
</body>
</html>
