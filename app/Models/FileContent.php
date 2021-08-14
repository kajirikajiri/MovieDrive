<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dirname',
        'displayname',
        'savedname',
        'link',
        'extension',
        'bytes',
    ];

    protected $hidden = [];

    protected $casts = [];

    protected $appends = [];


    public static function encriptLink($link)
    {
        return bin2hex(openssl_encrypt($link, 'AES-128-ECB', 'passkey'));
    }

    public static function decryptLink($link)
    {
        return openssl_decrypt(hex2bin($link), 'AES-128-ECB', 'passkey');
    }

    public static function combinePath(string $dirname, string $filename)
    {
        if ($filename) {
            return rtrim($dirname, '\\/') . '/' . $filename;
        } else {
            return rtrim($dirname, '\\/');
        }
    }
}
