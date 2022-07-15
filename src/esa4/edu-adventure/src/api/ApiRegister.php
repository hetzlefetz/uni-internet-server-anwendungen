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
        if (!array_key_exists("method", $_POST)) {
            error_log("method not found");
            Utils::addErrorToSession("Method Not Allowed");
            Utils::redirect("http://localhost/register", 405);
            exit;
            return;
        }
        switch ($_POST["method"]) {
            case "register":
                ApiRegister::doRegister();
                break;
            case "delete":
                ApiRegister::doDelete();
                break;
            case "makeAuthor":
                ApiRegister::addRole("Author");
                break;
            case "makeAdmin":
                ApiRegister::addRole("Admin");
                break;
        }
    }

    static function doRegister()
    {
        $wrapper = new DbWrapper();
        if ($_POST["password"] != $_POST["confirm_password"]) {

            Utils::addErrorToSession("Passwörter müssen übereinstimmen");
            Utils::redirect("http://localhost/register", 400);
            return;
        } else {
            if ($wrapper->registerUser($_POST["email"], $_POST["password"], $_POST["username"])) {
                Utils::redirect("http://localhost");
            } else {
                Utils::redirect("http://localhost/register", 400);
            }
        }
    }

    static function doDelete()
    {
        $id = $_POST["id"];
        $wrapper = new DbWrapper();
        $wrapper->deleteUser($id);
        Utils::redirect("http://localhost/profile", 200);
    }

    static function addRole($role): void
    {

        $id = $_POST["id"];
        $wrapper = new DbWrapper();
        $user = $wrapper->getUserById($id);
        if (in_array($role, $user->getRoles())) {
            Utils::redirect("http://localhost/profile", 200);
            return; // nothing to do;
        }
        $wrapper->addRoleToUser($id, $role);
        Utils::redirect("http://localhost/profile", 200);
        return;
    }
}
