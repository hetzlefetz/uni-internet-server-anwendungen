<?php

namespace EduAdventure\Api;

require_once __DIR__ . '/../../vendor/autoload.php';

use EduAdventure\Db\DbWrapper;
use EduAdventure\Utils\Utils;

class ApiRegister
{

    static function makeApi()
    {
        $post = function () {
            return ApiRegister::doPost();
        };

        return Utils::mapToMethod(["POST" => $post]);
    }

    static function doPost()
    {

        $wrapper = new DbWrapper();
        if ($_POST["password"] != $_POST["confirm_password"]) {
            $referer = "http://localhost/register";
            header("Location:" . $referer);
            exit();
            return;
        } else {

            $wrapper->registerUser($_POST["email"], $_POST["password"], $_POST["username"]);
            $referer = $_SERVER['HTTP_REFERER'];
        }
    }
}