<?php namespace App\Libraries;

class Config
{
    /**
     * The loaded configuration
     *
     * @var array
     */
    protected $config = [];


    /**
     * @param array $configs Array containing paths to the config files
     */
    public function __construct(array $configs)
    {
        foreach ($configs as $file) {
            $config = is_file($file) ? require $file : null;

            if (is_array($config)) {
                $this->config = array_replace_recursive($this->config, $config);
            }
        }
    }


    /**
     * Get a value from the config
     *
     * @param  string $section
     * @param  string $key
     * @return mixed
     */
    public function get($section, $key = null)
    {
        if (is_null($key)) {
            return $this->config[$section] ?? [];
        }

        return $this->config[$section][$key] ?? null;
    }
}
