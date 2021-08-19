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

    /**
     * @param array $data
     * @return bool
     * @throws AuthException
     */
    public function register(array $data): bool
    {
        if (empty($data['username'])){
            throw new AuthException('Поле - Имя пользователя не должно быть пустым');
        }
        if (empty($data['email'])){
            throw new AuthException('Поле - Почта не должна быть пустой');
        }
        if (empty($data['password'])){
            throw new AuthException('Поле - Пароль не должно быть пустым');
        }
        if ($data['password'] !== $data['confirm_password']){
            throw new AuthException('Пароль и Подтвержденный пароль должны совпадать');
        }

        $statement = $this->database->getCollection()->prepare(
            'INSERT INTO user (email, username, password) VALUES (:email, :username, :password)'
        );

        $statement->execute([
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);

        return true;
    }
}