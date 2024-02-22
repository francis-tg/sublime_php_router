<?php
namespace Cisco\SublimeDB;
use Francis\SublimePhp\ORM\ORM;


class Db extends ORM {
    public function __construct() {
        self::connect();
        self::createTable(
            "user",
            [
                "username" => "varchar(255)",
                "password" => "varchar(255)",
                "email" => "varchar(255)",
                "name" => "varchar(255)",
                "avatar" => "blob"
            ]
        );
    }
}