<?php namespace App\Controllers;

use App\Libraries\Request;
use App\Libraries\Views;
use App\Services\Users;
use App\Libraries\Session;

class UsersController extends BaseController
{
    /**
     * @var App\Libraries\Request
     */
    protected $request;

    /**
     * @var App\Libraries\Views
     */
    protected $views;

    /**
     * @var App\Services\Users
     */
    protected $users;

    /**
     * @var App\Libraries\Session
     */
    protected $session;


    /**
     * @param App\Libraries\Request $request
     * @param App\Libraries\Views   $views
     * @param App\Services\Users    $users
     * @param App\Libraries\Session $session
     */
    public function __construct(Request $request, Views $views, Users $users, Session $session)
    {
        $this->request = $request;
        $this->views   = $views;
        $this->users   = $users;
        $this->session = $session;

        // Make the current user accessible to all views
        $this->views->addGlobalData([
            'user' => $this->users->getCurrentUser(),
        ]);
    }


    /**
     * Show login view
     *
     * @return string
     */
    public function showLogin()
    {
        return $this->views->render('login');
    }


    /**
     * Log in a user
     *
     * @return string Serialized json
     */
    public function login()
    {
        $token = $this->request->post('token');

        if (!$this->session->verifyCsrf('login-form', $token)) {
            return $this->jsonResponse(null, 'Seems like the session has expired. Please reload the page and try again.');
        }

        $email    = $this->request->post('email');
        $password = $this->request->post('password');

        if ($email && $password) {
            if ($this->users->login($email, $password)) {
                return $this->jsonResponse();
            }
        }

        return $this->jsonResponse(null, 'Invalid credentials');
    }


    /**
     * Log the current user out
     */
    public function logout()
    {
        $this->users->logout();
        $this->redirect('/login');
    }


    /**
     * Show register view
     *
     * @return string
     */
    public function showRegister()
    {
        return $this->views->render('register');
    }


    /**
     * Register a user
     *
     * @return string Serialized json
     */
    public function register()
    {
        $token = $this->request->post('token');

        if (!$this->session->verifyCsrf('register-form', $token)) {
            return $this->jsonResponse(null, 'Seems like the session has expired. Please reload the page and try again.');
        }

        $data = [
            'name'             => $this->request->post('name'),
            'email'            => $this->request->post('email'),
            'password'         => $this->request->post('password'),
            'password_confirm' => $this->request->post('password_confirm'),
        ];

        $validate = $this->users->validate($data);
        if ($validate !== true) {
            return $this->jsonResponse(null, implode('<br />', $validate));
        }

        // Remove the password confirm since we don't want
        // to store it
        unset($data['password_confirm']);

        $user = $this->users->add($data);
        if ($user && $this->users->login($data['email'], $data['password'])) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(null, 'Error saving the user');
    }

}
