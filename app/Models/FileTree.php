<?php

namespace App\Models;

use App\Http\Controllers\DashboardController;

class FileTree
{
    const SEPARATOR = '/';

    public $text;
    public $full_path;
    public $nodes;
    public $depth = 0;

    public function __construct(string $root)
    {
        $array = explode(self::SEPARATOR, $root);
        $this->text = count($array) == 1 ? "マイディレクトリ" : $array[count($array) - 1];
        $this->full_path = $root;
        $this->link = DashboardController::encriptLink($root);
        $this->nodes = array();
        $this->depth = count($array);
    }
}
