<?php namespace App\Services;

use App\Libraries\Database;
use App\Libraries\Session;

class Users
{
    /**
     * @var Database
     */
    protected $db;

    /**
     * @var Session
     */
    protected $session;

    /**
     * The current logged in user
     *
     * @var array|null
     */
    protected $currentUser;


    /**
     * @param Database $db
     * @param Session  $session
     */
    public function __construct(Database $db, Session $session)
    {
        $this->db      = $db;
        $this->session = $session;

        if ($session->has('currentUser')) {
            $this->currentUser = $session->get('currentUser');
        }
    }


    /**
     * Add a new user
     *
     * @param  array       $data
     * @return array|false On success, return the new user record
     */
    public function add(array $data)
    {
        $data['created']  = date('Y-m-d H:i:s');
        $data['updated']  = date('Y-m-d H:i:s');
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, created, updated)
            VALUES (:name, :email, :password, :created, :updated)";

        $id = $this->db->insert($sql, $data);

        if (!$id) {
            return false;
        }

        return $this->byId($id);
    }


    /**
     * Update a user
     *
     * @param  int         $id
     * @param  array       $data
     * @return array|false On success, return the updated user record
     */
    public function update($id, array $data)
    {
        // Get the current user data
        $user = $this->find($id);

        if (!$user) {
            return false;
        }

        // Prepare the user data
        $data['updated']  = date('Y-m-d H:i:s');

        if (array_key_exists('password', $data)) {
            if (!empty($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT, [
                    'cost' => 10, // Arbitrary number that should be changed to fit the server
                ]);
            } else {
                unset($data['password']);
            }
        }

        // Add any missing fields from the current user data.
        // Doing this will make it possible to only pass in some fields
        // without messing with the existing data
        foreach ($user as $key => $value) {
            if (empty($data[$key])) {
                $data[$key] = $value;
            }
        }

        $sql = "INSERT INTO users (name, email, password, updated)
            VALUES (:name, :email, :password, :updated)";

        return $this->db->update($sql, $data)
            ? $this->byId($id)
            : false;
    }


    /**
     * Get a user by id
     *
     * @param  int        $id
     * @return array|null
     */
    public function byId($id)
    {
        $sql = "SELECT id, name, email, created, updated FROM users WHERE id = :id";

        $result = $this->db->select($sql, [
            'id' => $id
        ], true);

        return $result ?: null;
    }


    /**
     * Check if an email already exists
     *
     * @param  string $email
     * @return bool
     */
    public function emailExists($email)
    {
        $sql = "SELECT count(*) as `count` FROM users WHERE email = :email";

        $result = $this->db->select($sql, [
            'email' => $email
        ], true);

        return $result && $result['count'] > 0;
    }


    /**
     * Get the current logged in user, if any
     *
     * @return array|null
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }


    /**
     * Authenticate a user and, if successful, set them as logged in
     *
     * @param  string  $email
     * @param  string  $password
     * @return boolean
     */
    public function login($email, $password)
    {
        $sql = "SELECT id, name, email, password, created, updated FROM users WHERE email = :email";

        $user = $this->db->select($sql, [
            'email' => $email
        ], true);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        // No need to keep the password hash stored in the session
        unset($user['password']);

        $this->session->set('currentUser', $user);

        return true;
    }


    /**
     * Log the current user out
     */
    public function logout()
    {
        $this->currentUser = null;
        $this->session->destroy();
    }


    /**
     * Validate a user
     *
     * @param  array      $data
     * @return array|bool       True if success or array with errors
     */
    public function validate(array $data)
    {
        $errors = [];

        if (trim($data['name']) !== $data['name']) {
            $errors[] = "Your name can't start or end with spaces";
        }

        if (strlen(trim($data['name'] ?? '')) < 2) {
            $errors[] = 'Your name must be at least 2 chars long';
        }

        if ($this->emailExists($data['email'])) {
            $errors[] = 'The email address is already registered';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'You must enter a valid email';
        }

        if (strlen(trim($data['password'])) < 8) {
            $errors[] = "The password must be at least 8 chars";
        } else {
            if (trim($data['password']) !== $data['password']) {
                $errors[] = "The password can't start or end with white spaces";
            }

            if ($data['password'] !== $data['password_confirm']) {
                $errors[] = "The passwords doesn't match";
            }
        }

        return $errors ?: true;
    }
}
