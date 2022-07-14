<?php

use EduAdventure\Api\ApiRegister;
use EduAdventure\Views\Landing;
use EduAdventure\Views\Login;
use EduAdventure\Views\Register;

require_once("vendor/autoload.php");

$request = $_SERVER['REQUEST_URI'];

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
    case '/api/register':
        ApiRegister::makeApi();
        break;
    default:
        Landing::getPage();
        break;
        break;
}