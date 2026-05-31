<?php

class ErrorController {

    public function notFound() {
        http_response_code(404);
        $pageTitle = "Page Not Found";
        require __DIR__ . "/../views/errors/404.php";
    }

    public function forbidden() {
        http_response_code(403);
        $pageTitle = "Forbidden";
        require __DIR__ . "/../views/errors/403.php";
    }
}
