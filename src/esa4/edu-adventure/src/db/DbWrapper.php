<?php

namespace EduAdventure\Db;

use DateTime;
use EduAdventure\Models\Game;
use EduAdventure\Models\Session;
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

    function isUserLoggedIn(): bool
    {
        if (!array_key_exists("SID", $_SESSION)) {
            error_log("hä?");
            return false;
        }
        $sid = $_SESSION["SID"];
        $session = $this->getSession($sid);
        $now = new DateTime();

        if ($now->getTimestamp() - $session->timestamp < 60 * 100) { //default session time: 100min
            return true;
        }
        error_log("SESSION TIMED OUT");
        $this->deleteSession($sid); //remove session that timed out
        return false;
    }

    function deleteSession(string $sid): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Session WHERE sid =  ?');
        $stmt->execute([$sid]);
        return true;
    }
    function getSession(string $sid): Session
    {

        $stmt = $this->pdo->prepare("SELECT sid, timestamp, uid FROM Session WHERE sid = ? limit 1");
        $stmt->execute([$sid]);

        $data = $stmt->fetch();

        $ret =  new Session($data["sid"], $data["timestamp"], $data["uid"], $this);



        return $ret;
    }
    function addSession(int $user_id): string
    {
        $guid = Utils::guidv4();
        $date = new DateTime();

        $stmt = $this->pdo->prepare('INSERT INTO SESSION(sid,timestamp,uid) VALUES(?,?,?)');
        if (!$stmt->execute([$guid, $date->getTimestamp(), $user_id])) {
            error_log("FAILED TO ADD SESSION");
            error_log($this->pdo->errorInfo()[2]);
            return "";
        } else {
            return $guid;
        }
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

    function createGame(string $name): int
    {
        $this->getSession($_SESSION["SID"])->getUser();
        $stmt = $this->pdo->prepare('INSERT INTO GAME(file,published,name) VALUES(?,?,?)');
        $stmt->execute(["", 0, $name]);
        return $this->pdo->lastInsertId();
    }

    function updatePublishState(int $id, int $newState): int
    {
        $stmt = $this->pdo->prepare('UPDATE GAME SET published = ? WHERE id = ?');
        return $stmt->execute([$newState, $id]);
    }

    function deleteGame(int $id): int
    {
        $stmt = $this->pdo->prepare('DELETE FROM GAME WHERE id = ?');
        return $stmt->execute([$id]);
    }

    function getAllGames()
    {
        $stmt = $this->pdo->prepare('SELECT id,file,published,name FROM Game WHERE published = 1');
        $stmt->execute();
        $games = $stmt->fetchAll();
        $ret = [];

        foreach ($games as $data) {

            array_push($ret, new Game($data["id"], $data["file"], $data["published"], $data["name"], $this));
        }
        return $ret;
    }

    function getGamesForUser(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT id,file,published,name FROM Game WHERE id IN (SELECT game_id From User_Game WHERE user_id=?)');
        $stmt->execute([$id]);
        $games = $stmt->fetchAll();
        $ret = [];

        foreach ($games as $data) {

            array_push($ret, new Game($data["id"], $data["file"], $data["published"], $data["name"], $this));
        }
        return $ret;
    }
    function addGameForUser(int $id, int $gameId)
    {
        $addRole = $this->pdo->prepare("INSERT INTO User_Game(user_id,game_id) VALUES(?,?)");
        $addRole->execute([$id, $gameId]);
    }
    function getGameAuthor(int $gameId): string
    {
        error_log("!!!!");
        error_log("$gameId");
        error_log("!!!!");
        $stmt = $this->pdo->prepare("SELECT handle FROM User where id = (SELECT user_id FROM User_Game WHERE game_id = ?) limit 1");
        $stmt->execute([$gameId]);
        $data = $stmt->fetch();
        error_log($data);
        return $data["handle"];
    }
    function getUserById(int $id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE id=?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        if (!Utils::isDefined($data["id"])) {
            return null;
        }
        return new User($data["id"], $data["email"], $data["password"], $data["handle"], $this);
    }
    function getUserByMail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE email=?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        if (!Utils::isDefined($data["id"])) {
            return null;
        }

        return new User($data["id"], $data["email"], $data["password"], $data["handle"], $this);
    }
    function getAllUsers(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User');
        $stmt->execute();
        $ret = [];
        $users = $stmt->fetchAll();
        foreach ($users as $data) {
            array_push($ret, new User($data["id"], $data["email"], $data["password"], $data["handle"], $this));
        }
        return $ret;
    }
    function loginUser(string $email, string $pw)
    {
        $potentialUser = $this->getUserByMail($email);

        if ($potentialUser == null) {
            return false;
        }

        if (!password_verify($pw, $potentialUser->pw)) {
            return false;
        }

        $sid = $this->addSession($potentialUser->id);
        $_SESSION["SID"] = $sid;
        return true;
    }

    function registerUser(string $email, string $pw, string $handle): bool
    {

        if ($this->isHandleAvailable($handle) == false) {
            Utils::addErrorToSession("Der Nutzername oder die Email wird bereits verwendet");
            error_log("Handle Exists");
            return false;
        }

        if ($this->isEmailAvailable($handle) == false) {
            Utils::addErrorToSession("Der Nutzername oder die Email wird bereits verwendet");
            error_log("Email Exists");
            return false;
        }
        error_log("Registering ...");

        $stmt = $this->pdo->prepare('INSERT INTO USER(email,password,handle) VALUES(?,?,?)');
        error_log("Registering ...");
        $ret =  $stmt->execute([$email, password_hash($pw, PASSWORD_DEFAULT), $handle]);
        error_log("Finished with: $ret");
        return $ret;
    }

    function deleteUser($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM User WHERE id =  ?');
        return $stmt->execute([$id]);
    }
    function addRoleToUser($id, $role)
    {
        $stmt = $this->pdo->prepare('SELECT id FROM ROLE WHERE name = ? limit 1');
        $stmt->execute([$role]);
        $data = $stmt->fetch();

        $addRole = $this->pdo->prepare("INSERT INTO User_Role(user_id,role_id) VALUES(?,?)");
        $addRole->execute([$id, $data["id"]]);
    }
}
