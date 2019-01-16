<?php namespace App;

use Closure;

class Container
{
    /**
     * Bound classes
     *
     * @var array
     */
    protected $bound     = [];

    /**
     * @var array
     */
    protected $aliases   = [];

    /**
     * Resolved class instances
     *
     * @var array
     */
    protected $instances = [];


    /**
     * Bind a class to the container
     *
     * @param  string         $abstract
     * @param  Closure|Object $concrete
     * @param  string         $alias
     * @return $this
     */
    public function bind($abstract, $concrete, $alias = null)
    {
        if (!$concrete instanceof Closure && is_object($concrete)) {
            $this->instances[$abstract] = $concrete;
        } else {
            $this->bound[$abstract] = $concrete;
        }

        if (!is_null($alias)) {
            $this->aliases[$alias] = $abstract;
        }

        return $this;
    }


    /**
     * Resolve a bound class
     *
     * @param  string $abstract Either a bound class name or alias
     * @return mixed
     */
    public function make($abstract)
    {
        if (array_key_exists($abstract, $this->aliases)) {
            $abstract = $this->aliases[$abstract];
        }

        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        if (array_key_exists($abstract, $this->bound) && $this->bound[$abstract] instanceof Closure) {
            return $this->instances[$abstract] = call_user_func_array($this->bound[$abstract], [$this]);
        }

        return $this->instances[$abstract] = new $abstract;
    }


    /**
     * Access a bound class through it's alias
     *
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->make($property);
    }
}
