<?php
namespace App\Classes;


class HomePageController
{
    private Viewer $viewer;

    public function __construct(Viewer $viewer)
    {
        $this->viewer = $viewer;
    }

    public function index(): void
    {
        $data = [
            "title" => "Головна сторінка - DIM.RIA",
            "currentTime" => date("H:i:s d.m.Y"),
            "message" => "Ласкаво просимо до DIM.RIA!"
        ];

        $this->viewer->render("home", $data);
    }
}
