<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Вхід - DIM.RIA'); ?></title>
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="/">DIM.RIA</a>
            </div>
            <ul class="nav-links">
                <li><a href="/"<?php echo ($currentPath === 'home' || $currentPath === '') ? ' class="active"' : ''; ?>>Головна</a></li>
                <li><a href="/login"<?php echo ($currentPath === 'login') ? ' class="active"' : ''; ?>>Увійти</a></li>
                <li><a href="/register"<?php echo ($currentPath === 'register') ? ' class="active"' : ''; ?>>Реєстрація</a></li>
            </ul>
        </nav>
    </header>

    <main class="auth-container">
        <div class="auth-forms">
            <form id="login-form" class="auth-form">
                <h2>Вхід в акаунт</h2>
                <div class="form-group">
                    <input id="login" type="text" placeholder="Login" required>
                </div>
                <div class="form-group">
                    <input id="email" type="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input id="phone" type="tel" placeholder="Phone" required>
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Пароль" required>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox"> Запам'ятати мене
                    </label>
                    <a href="#" class="forgot-password">Забули пароль?</a>
                </div>
                <button type="submit" class="auth-button">Увійти</button>
                <div class="social-auth">
                    <p>Або увійдіть через:</p>
                    <div class="social-buttons">
                        <button type="button" class="social-btn google">Google</button>
                        <button type="button" class="social-btn facebook">Facebook</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <script src="scripts/login.js"></script>
</body>
</html>
