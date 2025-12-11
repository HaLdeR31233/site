<?php
namespace App\Classes;

use Monolog\Logger;
use Carbon\Carbon;
use Symfony\Component\VarDumper\VarDumper;
use Ramsey\Uuid\Uuid;

class HomePageController
{
    private Viewer $viewer;
    private Logger $logger;

    public function __construct(Viewer $viewer, Logger $logger)
    {
        $this->viewer = $viewer;
        $this->logger = $logger;
    }

    public function index(): void
    {
        $this->logger->info("Home page accessed");

        $sessionId = Uuid::uuid4()->toString();
        $this->logger->info("Generated session ID", ['sessionId' => $sessionId]);

        $testData = [
            'user' => [
                'id' => 123,
                'name' => 'Тестовый пользователь',
                'preferences' => [
                    'theme' => 'dark',
                    'language' => 'uk'
                ]
            ],
            'request' => [
                'method' => $_SERVER['REQUEST_METHOD'],
                'uri' => $_SERVER['REQUEST_URI'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]
        ];

        if (getenv('APP_DEBUG') === 'true') {
            VarDumper::dump($testData);
        }

        $this->logger->debug("Prepared data for view", [
            'data_keys' => array_keys($testData),
            'session_id' => $sessionId
        ]);

        $data = [
            "title" => "Головна сторінка - DIM.RIA",
            "currentTime" => Carbon::now()->format("H:i:s d.m.Y"),
            "message" => "Ласкаво просимо до DIM.RIA!",
            "sessionId" => $sessionId
        ];

        $this->viewer->render("home", $data);
    }
}
