<?php
namespace DB;
use Migration\Comment;
use Migration\Department;
use Migration\Post;
use Migration\User;
use Migration\File;

require_once '../vendor/autoload.php';

new Migration();

// MKBoard 폴더에서 다음 명령어 실횅  php .\DB\Migration.php
class Migration
{
    public function __construct()
    {
        $this->user = new User();
        $this->post = new Post();
        $this->comment = new Comment();
        $this->department = new Department();
        $this->file = new File();

        echo "[Migration Start]\n";
        $this->user->migrate();
        $this->post->migrate();
        $this->comment->migrate();
        $this->department->migrate();
        $this->file->migrate();
        echo "[Migration End]\n";
    }
}