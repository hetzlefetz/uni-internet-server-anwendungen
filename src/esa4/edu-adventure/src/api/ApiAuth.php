<?php

namespace EduAdventure\Api;

require_once __DIR__ . '/../../vendor/autoload.php';

use EduAdventure\Db\DbWrapper;
use EduAdventure\Utils\Utils;

class ApiAuth
{

    static function makeApi()
    {
        $post = function () {
            return ApiAuth::doPost();
        };

        return Utils::mapToMethod(["POST" => $post]);
    }

    static function doPost()
    {
        if (!array_key_exists("method", $_POST)) {
            error_log("method not found");
            Utils::addErrorToSession("Method Not Allowed");
            Utils::redirect("http://localhost/register", 405);
            exit;
            return;
        }
        switch ($_POST["method"]) {
            case "login":
                ApiAuth::doLogin();
                break;
            case "logout":
                ApiAuth::doLogout();
                break;
        }
    }
    static function doLogin()
    {

        $email = $_POST["email"];
        $password = $_POST["password"];


        $wrapper = new DbWrapper();
        error_log("loggin in with: $email and $password");

        if ($wrapper->loginUser($email, $password)) {
            error_log("login succsess");
            error_log("redirexting");
            Utils::redirect("http://localhost/profile", 200);
            return;
        } else {
            error_log("loggin failed");

            Utils::addErrorToSession("Login fehlgeschlagen!");
            Utils::redirect("http://localhost/login", 400);
        }
    }
    static function doLogout()
    {
        $wrapper = new DbWrapper();
        if ($wrapper->isUserLoggedIn()) {
            $wrapper->deleteSession($_SESSION["SID"]);
            unset($_SESSION["SID"]);
        }
        Utils::redirect("http://localhost", 200);
    }
}
