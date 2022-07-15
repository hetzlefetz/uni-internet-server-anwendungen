<?php

namespace EduAdventure\Models;

use EduAdventure\Db\DbWrapper;
use PDO;

require_once __DIR__ . '/../../vendor/autoload.php';

class Session
{
    public string $sid = "";
    public int $timestamp = -1;
    public int $uid = -1;
    private ?DbWrapper $db = null;
    private ?User $user = null;


    function __construct(string $sid, int $timestamp, int $uid, DbWrapper $db)
    {
        $this->sid = $sid;
        $this->timestamp = $timestamp;
        $this->uid = $uid;
        $this->db = $db;
    }

    function getUser()
    {
        if ($this->user == null) {
            $this->user =  $this->db->getUserById($this->uid);
        }
        return $this->user;
    }
}
