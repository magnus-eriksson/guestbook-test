<?php namespace App\Libraries;

use Closure;

class Router
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Registered routes
     *
     * @var array
     */
    protected $routes;

    /**
     * Callback if no matched route was found
     *
     * @var mixed
     */
    protected $notFound;

    /**
     * The callback resolver
     *
     * @var Closure
     */
    protected $resolver;


    /**
     * Placeholders for dynamic route params
     * @var array
     */
    protected $placeholders = [
        '\(\:any\)'      => '([^\/]+)',
        '\(\:num\)'      => '([0-9]+)',
        '\(\:alpha\)'    => '([a-zA-Z]+)',
        '\(\:alphanum\)' => '([0-9a-zA-Z]+)',
    ];


    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

        // Set the default callback for notFound
        $this->notFound = function () {
            http_response_code(404);
            return '404 - Not found';
        };
    }


    /**
     * Add a GET route
     *
     * @param  string $pattern
     * @param  string $callback
     * @return $this
     */
    public function get($pattern, $callback, $middleware = null)
    {
        return $this->addRoute('GET', $pattern, $callback, $middleware);
    }


    /**
     * Add a POST route
     *
     * @param  string $pattern
     * @param  string $callback
     * @return $this
     */
    public function post($pattern, $callback, $middleware = null)
    {
        return $this->addRoute('POST', $pattern, $callback, $middleware);
    }


    /**
     * Add a route
     *
     * @param  string $pattern
     * @param  string $callback
     * @return $this
     */
    public function addRoute($method, $pattern, $callback, $middleware = null)
    {
        // Normalize the method and the path
        $method  = strtoupper($method);
        $pattern = '/' . trim($pattern, '/');

        $this->routes[$method][$pattern] = [
            'callback'   => $callback,
            'middleware' => $middleware,
        ];

        return $this;
    }


    /**
     * Set the callback to use when no route was found
     *
     * @param  Closure $callback
     * @return $this
     */
    public function notFound(Closure $callback)
    {
        $this->notFound = $callback;

        return $this;
    }


    /**
     * Set a resolver for route callbacks
     *
     * @param Closure $resolver
     * @return $this
     */
    public function setResolver(Closure $resolver)
    {
        $this->resolver = $resolver;

        return $this;
    }


    /**
     * Dispatch the router
     *  - You can pass the method and path the router should use. Good for testing
     *
     * @param  string $method
     * @param  string $path
     * @return string
     */
    public function dispatch($method = null, $path = null)
    {
        // Normalize the method and the path
        $method = strtoupper($method ?: $this->request->getRequestMethod());
        $path   = '/' . trim($path ?: $this->request->getRequestPath(), '/');

        $route = $this->getMatchedRoute($method, $path);

        if (!$route) {
            return $this->executeCallback($this->notFound);
        }

        if ($route['middleware']) {
            $response = $this->executeCallback($route['middleware'], $route['params']);

            if (!is_null($response)) {
                // The middleware returned something other than null, which means
                // that we should use that result instead
                return $response;
            }
        }

        return $this->executeCallback($route['callback'], $route['params']);
    }


    /**
     * Get matched route
     *
     * @param  string $method
     * @param  string $path
     * @return mixed
     */
    protected function getMatchedRoute($method, $path)
    {
        if (empty($this->routes[$method])) {
            // No routes with this method has been registered
            return false;
        }

        $placeholders = array_keys($this->placeholders);
        $placeRegex   = array_values($this->placeholders);

        foreach ($this->routes[$method] as $pattern => $route) {
            // Quote the pattern so we safely can use it in regex
            $pattern = preg_quote($pattern, '~');

            // Add the delimiter and replace the route placeholders
            // to their regex counterparts
            $pattern = '~^' . $pattern . '$~';
            $pattern = str_replace($placeholders, $placeRegex, $pattern);

            preg_match($pattern, $path, $matches);

            if (!$matches) {
                // No match, let's skip to the next iteration
                continue;
            }

            // Remove the first preg match since we just want the dynamic parameters
            unset($matches[0]);

            return [
                'callback'   => $route['callback'],
                'params'     => $matches,
                'middleware' => $route['middleware'],
            ];
        }

        return false;
    }


    /**
     * Execute a callback
     *
     * @param  string $callback
     * @param  array  $params
     * @return mixed
     */
    protected function executeCallback($callback, array $params = [])
    {
        if (is_string($callback) && strpos($callback, '@') !== false) {
            // Break the string up to an array [class => method]
            $callback = explode('@', $callback);
        }

        if (is_array($callback) && count($callback) == 2) {
            // We have an array with [class => method], let's use the resolver
            // to resolve the class
            if ($this->resolver) {
                $callback[0] = call_user_func_array($this->resolver, [$callback[0]]);
            }
        }

        return call_user_func_array($callback, $params);
    }
}
