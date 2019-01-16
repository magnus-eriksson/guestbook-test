<?php namespace App\Middlewares;

use App\Services\Users;

class Auth
{
    protected $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function requireAuth()
    {
        if (!$this->users->getCurrentUser()) {
            return 'This route requires you to be logged in';
        }
    }
}
