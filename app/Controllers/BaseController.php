<?php namespace App\Controllers;

class BaseController
{
    /**
     * Return a normalized json response
     *
     * @param  string $content
     * @param  array  $errors
     * @return string Serialized json object
     */
    protected function jsonResponse($content = null, $error = null)
    {
        $response = [
            'success' => true,
            'data'    => $content,
            'error'   => $error,
        ];

        if ($error) {
            $response['success'] = false;
        }

        header('Content-Type: application/json');

        return json_encode($response);
    }


    public function redirect($url)
    {
        header('Location: ' . $url, true, 302);
        exit;
    }
}
