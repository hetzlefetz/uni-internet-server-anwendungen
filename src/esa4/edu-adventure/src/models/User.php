<?php

namespace EduAdventure\Models;

use EduAdventure\Db\DbWrapper;

require_once __DIR__ . '/../../vendor/autoload.php';

class User
{
    public int $id;
    public string $email;
    public string $handle;
    public string $pw;
    private DbWrapper $db;
    private array $roles = [];

    function __construct(int $id, string $email, string $pw, string $handle, DbWrapper $db)
    {
        $this->id = $id;
        $this->email = $email;
        $this->pw = $pw;
        $this->handle = $handle;
        $this->db = $db;
    }

    public function getRoles(): array
    {
        if ($this->roles == null || count($this->roles) == 0) {
            $this->roles = $this->db->getRolesForUser($this->id);
        }
        return $this->roles;
    }

    public function getGames(): array
    {
        if ($this->isAuthor() == false) {
            return [];
        }
        return $this->db->getGamesForUser($this->id);
    }

    public function addGame($gameId)
    {
        return $this->db->addGameForUser($this->id, $gameId);
    }

    public function isAdmin(): bool
    {
        return in_array("Admin", $this->getRoles());
    }
    public function isContributor(): bool
    {
        return in_array("Contributor", $this->getRoles());
    }
    public function isUser(): bool
    {
        return in_array("User", $this->getRoles());
    }
    public function isAuthor(): bool
    {
        return in_array("Author", $this->getRoles());
    }
}
