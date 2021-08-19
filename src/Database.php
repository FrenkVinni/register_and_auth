<?php

declare(strict_types=1);
namespace App;

use http\Exception\InvalidArgumentException;
use PDO;
class Database
{
    private PDO $connection;

    public function  __construct(string $dsn, string $username = '', string $password ='')
    {
        try {
            $this->connection = new PDO($dsn, $username, $password);
        }catch (\PDOException $exception) {
            throw new InvalidArgumentException('Database error: '. $exception->getMessage());
        }
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function getCollection(): PDO
    {
        return $this->connection;
    }
}