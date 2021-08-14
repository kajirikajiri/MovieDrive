<?php

namespace App\Models;

class FileTree
{
    const SEPARATOR = '/';

    public $text;
    public $full_path;
    public $nodes;
    public $depth = 0;

    public function __construct(string $root)
    {
        $root = ltrim($root, 'public/');
        $array = explode(self::SEPARATOR, $root);
        $this->text = count($array) == 1 ? "マイディレクトリ" : $array[count($array) - 1];
        $this->full_path = $root;
        $this->link = FileContent::encriptLink($root);
        $this->nodes = array();
        $this->depth = count($array);
    }
}
