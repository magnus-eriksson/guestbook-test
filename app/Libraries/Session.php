<?php namespace App\Libraries;

class Session
{
    /**
     * Session status
     *
     * @var boolean
     */
    protected $started = false;


    public function __construct()
    {
        if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
            // Only start the session if headers haven't been sent
            // and we don't already have started it.
            session_start();
            $this->started = true;
        }
    }


    /**
     * Get a value from the session
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->started) {
            return;
        }

        return $_SESSION[$key] ?? null;
    }


    /**
     * Check if the session contains a specific key
     *
     * @param  string  $key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $_SESSION);
    }


    /**
     * Set a value in the session
     *
     * @param  string $key
     * @param  mixed  $data
     * @return $this
     */
    public function set($key, $data)
    {
        $_SESSION[$key] = $data;

        return $this;
    }


    /**
     * Get a csrf token
     *
     * @param  string  $name
     * @param  boolen $regenerate Force token regeneration
     * @return string
     */
    public function csrf($name, $regenerate = false)
    {
        $key = 'csrf_' . $name;

        if ($regenerate || !$this->has($key)) {
            $token = bin2hex(random_bytes(64));
            $this->set($key, $token);
        }

        return $this->get($key);
    }


    /**
     * Validate a csrf token
     *
     * @param  string $name
     * @param  string $token
     * @return boolean
     */
    public function verifyCsrf($name, $token)
    {
        if (strlen($token) < 64) {
            return;
        }

        $key = 'csrf_' . $name;

        return hash_equals($this->get($key), $token);
    }


    /**
     * Unset/delete a session propery
     *
     * @param  string $key
     * @return $this
     */
    public function unset($key)
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }

        return $this;
    }


    /**
     * Clear & destroy all session data
     *
     * @return $this
     */
    public function destroy()
    {
        if (isset($_COOKIE[session_name()])) {
            // Invalidate the session cookie
            setcookie( session_name(), "", time()-3600);
        }

        // Remove all data from the super global
        $_SESSION = [];

        // Remove session data from disk
        session_destroy();

        return $this;
    }
}
