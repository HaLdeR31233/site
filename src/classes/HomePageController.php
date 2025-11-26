<?php
namespace App\Classes;

use Monolog\Logger;
use Carbon\Carbon;

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

        $data = [
            "title" => "Головна сторінка - DIM.RIA",
            "currentTime" => Carbon::now()->format("H:i:s d.m.Y"),
            "message" => "Ласкаво просимо до DIM.RIA!"
        ];

        $this->viewer->render("home", $data);
    }
}