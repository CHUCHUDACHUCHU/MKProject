<?php
namespace DB;
use Migration\Comment;
use Migration\Post;
use Migration\User;

require_once __DIR__."/../bootstrap.php";

new Migration();

// MKBoard 폴더에서 다음 명령어 실횅  php .\DB\Migration.php
class Migration
{
    public function __construct()
    {
        $this->user = new User();
        $this->post = new Post();
        $this->comment = new Comment();

        echo "[Migration Start]\n";
        $this->user->migrate();
        $this->post->migrate();
        $this->comment->migrate();
        echo "[Migration End]\n";
    }
}