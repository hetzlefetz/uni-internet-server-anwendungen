<?php

namespace EduAdventure\Models;

require_once __DIR__ . '/../../vendor/autoload.php';

class User
{
    public int $id;
    public string $email;
    public string $handle;
    public string $pw;
    public array $roles;

    function __construct(int $id, string $email, string $pw, string $handle, array $roles)
    {
        $this->id = $id;
        $this->email = $email;
        $this->pw = $pw;
        $this->handle = $handle;
        $this->roles = $roles;
    }
}
