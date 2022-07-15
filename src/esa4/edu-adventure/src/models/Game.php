<?php

namespace EduAdventure\Models;

use EduAdventure\Db\DbWrapper;
use PDO;

require_once __DIR__ . '/../../vendor/autoload.php';

class Game
{
    public int $id;
    public string $file;
    public int $published;
    public string $title;
    private ?DbWrapper $db = null;

    function __construct(int $id, string $file, int $published, string $title, DbWrapper $db)
    {
        $this->id = $id;
        $this->file = $file;
        $this->published = $published;
        $this->title = $title;
        $this->db = $db;
    }

    function getAuthor()
    {
        return $this->db->getGameAuthor($this->id);
    }
}
