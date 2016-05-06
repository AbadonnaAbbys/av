<?php

/**
 * Created by PhpStorm.
 * User: Abadonna
 * Date: 05.05.2016
 * Time: 0:11
 */
class DB {
    const HOST = '127.0.0.1';
    const PORT = null;
    const SOCKET = null;
    const NAME = 'aw';
    const USERNAME = 'aw';
    const PASSWORD = 'aw';

    /**
     * @var DB
     */
    private static $entity;
    /**
     * @var \mysqli
     */
    private $connection;

    /**
     * DB constructor.
     */
    private function __construct() {
        $this->connection = new mysqli(self::HOST, self::USERNAME, self::PASSWORD, self::NAME, self::PORT, self::SOCKET);
        if ($this->connection->connect_errno) {
            exit('Sorry. Something go wrong.');
        }
    }

    /**
     * @return \mysqli
     */
    public static function getConnection() {
        if (is_null(self::$entity)) {
            self::$entity = new DB();
        }

        return self::$entity->connection;
    }
}