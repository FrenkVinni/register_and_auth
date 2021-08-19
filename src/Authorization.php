<?php


declare(strict_types=1);
namespace App;


class Authorization
{
    /**
     * @var Database
     */
    private Database $database;

    /**
     * Authorization constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function authorization(array $data): bool
    {
        $statement = $this->database->getCollection()->prepare(
            ''
        );


    }
}