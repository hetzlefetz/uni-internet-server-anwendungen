<?php

use EduAdventure\Api\ApiAuth;
use EduAdventure\Api\ApiGame;
use EduAdventure\Api\ApiRegister;
use EduAdventure\Views\Landing;
use EduAdventure\Views\Login;
use EduAdventure\Views\Profile;
use EduAdventure\Views\Register;

require_once("vendor/autoload.php");

$request = $_SERVER['REQUEST_URI'];
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_log("Access");
switch ($request) {
    case '':
    case '/':
        Landing::getPage();
        break;
    case '/login':
        Login::getPage();
        break;
    case '/register':
        Register::getPage();
        break;
    case '/profile':
        Profile::getPage();
        break;
    case '/api/register':
        ApiRegister::makeApi();
        break;
    case '/api/auth':
        ApiAuth::makeApi();
        break;
    case '/api/game':
        ApiGame::makeApi();
        break;
    default:
        Landing::getPage();
        break;
        break;
}
