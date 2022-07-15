<?php

namespace EduAdventure\Api;

require_once __DIR__ . '/../../vendor/autoload.php';

use EduAdventure\Db\DbWrapper;
use EduAdventure\Utils\Utils;

class ApiGame
{

    static function makeApi()
    {
        $post = function () {
            return ApiGame::doPost();
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
            case "create":
                ApiGame::doCreate();
                break;
            case "delete":
                ApiGame::doDelete();
                break;
            case "publish":
                ApiGame::setPublish(1);
                break;
            case "unpublish":
                ApiGame::setPublish(0);
                break;
        }
    }

    static function doCreate()
    {
        $title = $_POST["title"];
        $wrapper = new DbWrapper();
        $game_id = $wrapper->createGame($title);
        $wrapper->getSession($_SESSION["SID"])->getUser()->addGame($game_id);
        Utils::redirect("http://localhost/profile", 200);
        return;
    }

    static function doDelete()
    {
        $id = $_POST["id"];
        $wrapper = new DbWrapper();
        $wrapper->deleteGame($id);
        Utils::redirect("http://localhost/profile", 200);
        return;
    }

    static function setPublish(int $newState)
    {
        $id = $_POST["id"];
        $wrapper = new DbWrapper();
        $wrapper->updatePublishState($id, $newState);
        Utils::redirect("http://localhost/profile", 200);
        return;
    }
}
