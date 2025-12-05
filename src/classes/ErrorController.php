<?php
namespace App\Classes;

use Monolog\Logger;

class ErrorController
{
    private Viewer $viewer;
    private Logger $logger;

    public function __construct(Viewer $viewer, Logger $logger)
    {
        $this->viewer = $viewer;
        $this->logger = $logger;
    }

    public function error404(): void
    {
        $this->logger->warning("404 error", [
            "uri" => $_SERVER["REQUEST_URI"],
            "referer" => $_SERVER["HTTP_REFERER"] ?? ""
        ]);

        http_response_code(404);
        $this->viewer->render("error404", [
            "title" => "404 - РЎС‚РѕСЂС–РЅРєСѓ РЅРµ Р·РЅР°Р№РґРµРЅРѕ"
        ]);
    }

    public function error500(): void
    {
        $this->logger->error("500 error occurred");

        http_response_code(500);
        $this->viewer->render("error500", [
            "title" => "500 - Р’РЅСѓС‚СЂС–С€РЅСЏ РїРѕРјРёР»РєР° СЃРµСЂРІРµСЂР°"
        ]);
    }

    public function error403(): void
    {
        $this->logger->warning("403 error - access denied", [
            "uri" => $_SERVER["REQUEST_URI"],
            "user" => $_SESSION["user"]["email"] ?? "anonymous"
        ]);

        http_response_code(403);
        $this->viewer->render("error403", [
            "title" => "403 - Р”РѕСЃС‚СѓРї Р·Р°Р±РѕСЂРѕРЅРµРЅРѕ"
        ]);
    }
}
