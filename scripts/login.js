(function initAuthValidation() {
    const form = document.getElementById('login-form');
    const loginInput = document.getElementById('login');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    if (!form || !loginInput || !emailInput || !phoneInput) return;

    const loginPattern = /^[a-zA-Z0-9_]{3,20}$/;
    const emailPattern = /^[\w.-]+@[\w.-]+\.[A-Za-z]{2,}$/;
    const phoneDigitsPattern = /\d{10,14}/;

    loginInput.addEventListener('input', () => {
        const original = loginInput.value;
        const cleaned = original.replace(/[^a-zA-Z0-9_]/g, '');
        if (original !== cleaned) {
            loginInput.value = cleaned;
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const loginValue = loginInput.value.trim();
        const emailValue = emailInput.value.trim();
        const phoneValue = phoneInput.value.trim();

        const isLoginValid = !!loginValue.match(loginPattern);
        const isEmailValid = !!emailValue.match(emailPattern);

        const hasPhoneDigits = phoneValue.search(phoneDigitsPattern) !== -1;

        if (!isLoginValid) {
            alert('Login некорректен: используйте 3-20 символов (латиница, цифры, _)');
            return;
        }
        if (!isEmailValid) {
            alert('Email некорректен');
            return;
        }
        if (!hasPhoneDigits) {
            alert('Phone некорректен: должно быть 10-14 цифр');
            return;
        }

        alert('Данные валидны!');
    });
})(); 
