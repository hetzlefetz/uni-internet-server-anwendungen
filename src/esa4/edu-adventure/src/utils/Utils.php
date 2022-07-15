<?php

namespace EduAdventure\Utils;

require_once __DIR__ . '/../../vendor/autoload.php';

class Utils
{


    static function isDefined($var): bool
    {
        return isset($var) && strlen(trim($var)) > 0;
    }

    static function guidv4(string $data = null): string
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    static function mapToMethod(array $map)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (array_key_exists("POST", $map)) {
                $map["POST"]();
            } else {
                http_response_code(400);
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (array_key_exists("GET", $map)) {
                $map["GET"]();
            } else {
                http_response_code(400);
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if (array_key_exists("PUT", $map)) {
                $map["PUT"]();
            } else {
                http_response_code(400);
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if (array_key_exists("DELETE", $map)) {
                $map["DELETE"]();
            } else {
                http_response_code(400);
            }
        }
        http_response_code(400);
    }

    static function redirect(string $url, int $status = 200, array $errors = [])
    {

        if (count($errors) > 0) {
            array_map(fn ($error) => Utils::addErrorToSession($error), $errors);
        }
        http_response_code($status);
        header("Location:" . $url);
        error_log("New location: $url");
        exit();
        return;
    }

    static function addErrorToSession(string $error)
    {
        if (!array_key_exists("ERRORS", $_SESSION)) {
            $_SESSION["ERRORS"] = [];
        }
        array_push($_SESSION["ERRORS"], $error);
    }
}
