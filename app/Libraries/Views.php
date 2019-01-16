<?php namespace App\Libraries;

use Exception;

class Views
{
    /**
     * Path to the views folder
     *
     * @var string
     */
    protected $path;

    /**
     * Global data, passed to all views
     *
     * @var array
     */
    protected $data = [];


    /**
     * @param string $path Path to the views folder
     */
    public function __construct($path, Session $session)
    {
        $this->path    = $path;
        $this->session = $session;
    }


    /**
     * Add global accessible data
     *
     * @param array $data
     */
    public function addGlobalData(array $data)
    {
        $this->data = array_replace_recursive($this->data, $data);
    }


    /**
     * Render a view
     *
     * @param  string $viewFile
     * @param  array  $viewData
     *
     * @throws Exception if the view file isn't found
     *
     * @return string
     */
    public function render($viewFile, array $viewData = [])
    {
        // Extract the data as variables, but don't overwrite existing
        extract(array_replace_recursive($this->data, $viewData), EXTR_SKIP);

        if (!is_file($this->getFullViewPath($viewFile))) {
            // Seems like we have an invalid template file
            throw new Exception("Unable to find the view {$templateFile}");
        }

        ob_start();

        include $this->getFullViewPath($viewFile);

        return ob_get_clean();
    }


    /**
     * Get a csrf token
     *
     * @param  string $name
     * @param  boolen $regenerate Force token regeneration
     * @return string
     */
    public function csrf($name, $regenerate = false)
    {
        return $this->session->csrf($name, $regenerate);
    }


    /**
     * Get the full path to a view
     *
     * @param  string $view
     * @return string
     */
    protected function getFullViewPath($view)
    {
        return $this->path . '/' . ltrim($view, '/') . '.php';
    }


    /**
     * Escape and Convert a string to htmlentities
     *
     * @param  string $string
     * @return string
     */
    protected function e($string)
    {
        return htmlentities($string);
    }
}
