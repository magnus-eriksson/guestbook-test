<?php namespace App\Libraries;

class Request
{
    /**
     * $_GET params
     *
     * @var array
     */
    protected $get    = [];

    /**
     * $_POST params
     *
     * @var array
     */
    protected $post   = [];

    /**
     * $_SERVER params
     *
     * @var array
     */
    protected $server = [];


    public function __construct()
    {
        $this->setGet($_GET);
        $this->setPost($_POST);
        $this->setServer($_SERVER);
    }


    /**
     * Manually set the $_GET values
     * - With this, you can override the data when you're, for example, writing tests
     *
     * @param  array $get
     * @return $this
     */
    public function setGet(array $get)
    {
        $this->get = $get;

        return $this;
    }


    /**
     * Manually set the $_POST values
     * - With this, you can override the data when you're, for example, writing tests
     *
     * @param  array $post
     * @return $this
     */
    public function setPost(array $post)
    {
        $this->post = $post;

        return $this;
    }


    /**
     * Manually set the $_SERVER values
     * - With this, you can override the data when you're, for example, writing tests
     *
     * @param  array $server
     * @return $this
     */
    public function setServer(array $server)
    {
        $this->server = $server;

        return $this;
    }


    /**
     * Get a value from the $_GET superglobal
     *
     * @param  string $key
     * @param  mixed  $fallback Returned if the key isn't found
     * @return mixed
     */
    public function get($key = null, $fallback = null)
    {
        if (is_null($key)) {
            // If we didn't get any key, return all
            return $this->get;
        }

        return array_key_exists($key, $this->get)
            ? $this->get[$key]
            : $fallback;
    }


    /**
     * Get a value from the $_POST superglobal
     *
     * @param  string $key
     * @param  mixed  $fallback Returned if the key isn't found
     * @return mixed
     */
    public function post($key = null, $fallback = null)
    {
        if (is_null($key)) {
            // If we didn't get any key, return all
            return $this->post;
        }

        return array_key_exists($key, $this->post)
            ? $this->post[$key]
            : $fallback;
    }


    /**
     * Get a value from the $_SERVER superglobal
     *
     * @param  string $key
     * @param  mixed  $fallback Returned if the key isn't found
     * @return mixed
     */
    public function server($key = null, $fallback = null)
    {
        if (is_null($key)) {
            // If we didn't get any key, return all
            return $this->server;
        }

        return array_key_exists($key, $this->server)
            ? $this->server[$key]
            : $fallback;
    }


    /**
     * Get the requested method
     *
     * @param  string $fallback
     * @return string
     */
    public function getRequestMethod($fallback = 'GET')
    {
        return $this->server('REQUEST_METHOD', $fallback);
    }


    /**
     * Get the requested path
     *
     * @param  string $fallback
     * @return string
     */
    public function getRequestPath($fallback = '/')
    {
        // Make sure we only get the path, not including the query string
        return strtok($this->server('REQUEST_URI'), '?');
    }
}
