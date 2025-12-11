<?php

/**
 * Файл контролера AboutMeController
 *
 * Контролер для сторінки "Про мене". Відповідає за відображення
 * інформації про розробника, університет та навички.
 *
 * @package     kn24_php
 * @subpackage  Controllers
 * @author      Єгор
 * @version     1.0.0
 */

namespace App\Classes;


/**
 * Клас AboutMeController
 *
 * Обробляє логіку для сторінки "Про мене".
 * Передає дані про розробника в шаблон через клас Viewer.
 */
class AboutMeController
{
    private Viewer $viewer;

    /** @var string Ім'я розробника */
    private string $name = "Єгор";

    /** @var string Назва університету */
    private string $university = "Ельворті";

    /** @var string Назва групи */
    private string $group = "КН-24";

    /** @var array Список навичок розробника */
    private array $skills = [
        'PHP 8',
        'Latte templates',
        'HTML/CSS',
        'JavaScript',
        'MySQL',
        'MVC'
    ];

    public function __construct(Viewer $viewer)
    {
        $this->viewer = $viewer;
    }

    /**
     * Метод show
     *
     * Основний метод контролера, який викликається роутером.
     * Передає дані в шаблон aboutme.latte
     *
     * @return void
     */
    public function show(): void
    {
        $data = [
            'title'   => 'Про мене',
            'myName'  => $this->name,
            'myUni'   => $this->university,
            'myGroup' => $this->group,
            'skills'  => $this->skills
        ];

        $this->viewer->render('aboutme', $data);
    }
}
