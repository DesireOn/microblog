<?php

namespace App\DataMapper;

use App\Entity\User;

/** Maps data from post request to a User entity. */
class UserMapper
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param array $data
     * @return User
     */
    public function toUser(array $data): User
    {
        if (isset($data['email'])) {
            $this->user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $this->user->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
        }

        return $this->user;
    }
}