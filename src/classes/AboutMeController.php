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

use Monolog\Logger;
use Symfony\Component\VarDumper\VarDumper;
use Ramsey\Uuid\Uuid;

/**
 * Клас AboutMeController
 *
 * Обробляє логіку для сторінки "Про мене".
 * Передає дані про розробника в шаблон через клас Viewer.
 */
class AboutMeController
{
    private Viewer $viewer;
    private Logger $logger;

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

    public function __construct(Viewer $viewer, Logger $logger)
    {
        $this->viewer = $viewer;
        $this->logger = $logger;
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
        $this->logger->info("About me page accessed");

        // Генерируем уникальный ID для страницы
        $pageId = Uuid::uuid4()->toString();

        // Создаем тестовые данные для демонстрации VarDumper
        $debugData = [
            'page_info' => [
                'controller' => 'AboutMeController',
                'method' => 'show',
                'page_id' => $pageId
            ],
            'developer_info' => [
                'name' => $this->name,
                'university' => $this->university,
                'group' => $this->group,
                'skills_count' => count($this->skills)
            ]
        ];

        // Используем VarDumper для отладки в режиме разработки
        if (getenv('APP_DEBUG') === 'true') {
            VarDumper::dump($debugData);
        }

        $this->logger->debug("About me data prepared", [
            'skills_count' => count($this->skills),
            'page_id' => $pageId
        ]);

        $data = [
            'title'   => 'Про мене',
            'myName'  => $this->name,
            'myUni'   => $this->university,
            'myGroup' => $this->group,
            'skills'  => $this->skills,
            'pageId'  => $pageId
        ];

        $this->viewer->render('aboutme', $data);
    }
}
