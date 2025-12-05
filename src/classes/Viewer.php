<?php
namespace App\Classes;

use Latte\Engine;
use Latte\Loaders\FileLoader;

class Viewer
{
    private Engine $latte;

    public function __construct(string $templateDir, string $tempDir)
    {
        $this->latte = new Engine();
        $this->latte->setLoader(new FileLoader($templateDir));
        $this->latte->setTempDirectory($tempDir);
    }

    public function render(string $template, array $params = []): void
    {
        $params["currentYear"] = date("Y");
        $params["baseUrl"] = $this->getBaseUrl();

        echo $this->latte->renderToString($template . ".latte", $params);
    }

    public function renderJson(array $data): void
    {
        header("Content-Type: application/json");
        echo json_encode($data);
    }

    public function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: " . $url, true, $statusCode);
        exit;
    }

    private function getBaseUrl(): string
    {
        $protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http";
        $host = $_SERVER["HTTP_HOST"];
        $script = dirname($_SERVER["SCRIPT_NAME"]);

        return $protocol . "://" . $host . ($script !== "/" ? $script : "");
    }
}
