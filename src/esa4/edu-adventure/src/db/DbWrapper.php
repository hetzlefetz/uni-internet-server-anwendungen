<?php

namespace EduAdventure\Db;

use DateTime;
use EduAdventure\Models\User;
use EduAdventure\Utils\Utils;

require_once __DIR__ . '/../../vendor/autoload.php';



class DbWrapper
{
    private \PDO $pdo;

    function __construct()
    {
        $this->pdo = (new SQLiteConnection())->connect();
    }

    function isHandleAvailable(string $handle)
    {
        $stmt = $this->pdo->prepare('SELECT count(*) as cnt FROM User WHERE lower(handle)=?');
        $stmt->execute([strtolower($handle)]);
        $data = $stmt->fetch();
        return $data["cnt"] == "0" ? true : false;
    }
    function isEmailAvailable(string $email)
    {
        $stmt = $this->pdo->prepare('SELECT count(*) as cnt FROM User WHERE lower(email)=?');
        $stmt->execute([strtolower($email)]);
        $data = $stmt->fetch();
        return $data["cnt"] == "0" ? true : false;
    }

    function addSession(int $user_id): string
    {
        $guid = Utils::guidv4();
        $date = new DateTime();

        $stmt = $this->pdo->prepare('INSERT INTO SESSION(sid,timestamp,uid) VALUES(?,?,?)');
        $stmt->execute([$guid, $date->getTimestamp(), $user_id]);

        return $guid;
    }
    function getRolesForUser(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT name FROM Role WHERE id IN (SELECT role_id From User_Role WHERE user_id=?)');
        $stmt->execute([$id]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $roles = [];
        foreach ($data as $key => $value) {
            array_push($roles, $value["name"]);
        }
        return $roles;
    }
    function getUserById(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE id=?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        if (!Utils::isDefined($data["id"])) {
            return null;
        }
        return new User($data["id"], $data["email"], $data["password"], $data["handle"], $this->getRolesForUser($data["id"]));
    }
    function getUserByMail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE email=?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        if (!Utils::isDefined($data["id"])) {
            return null;
        }

        return new User($data["id"], $data["email"], $data["password"], $data["handle"], $this->getRolesForUser($data["id"]));
    }
    function loginUser(string $email, string $pw)
    {
        $potentialUser = $this->getUserByMail($email);

        if (!Utils::isDefined($potentialUser)) {
            return false;
        }

        if (!password_verify($pw, $potentialUser->password)) {
            return false;
        }

        $sid = $this->addSession($potentialUser->id);
        $_SESSION["sid"] = $sid;
        return true;
    }

    function registerUser(string $email, string $pw, string $handle)
    {
        $output = "<br />";
        $output .= "Is Administrator available:";
        $output .= $this->isHandleAvailable("Administrator") ? "Yes" : "No";
        $output .= "<br />";

        $output .= "Is aDmin@eXample.Com available:";
        $output .= $this->isEmailAvailable("aDmin@eXample.Com") ? "Yes" : "No";
        $output .= "<br />";

        $output .= "Is Horst available:";
        $output .= $this->isHandleAvailable("Horst") ? "Yes" : "No";
        $output .= "<br />";

        $output .= "Is Horst@example.com available:";
        $output .= $this->isEmailAvailable("Horst@example.com") ? "Yes" : "No";
        $output .= "<br />";

        echo $output;
        $this->getRolesForUser(1);
    }
}
