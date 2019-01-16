<?php namespace App\Controllers;

use App\Libraries\Request;
use App\Libraries\Views;
use App\Services\Entries;
use App\Services\Users;
use App\Libraries\Session;

class EntitiesController extends BaseController
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
     * @var App\Services\Entries
     */
    protected $entries;

    /**
     * @var App\Services\Users
     */
    protected $users;


    /**
     * @param App\Libraries\Request $request
     * @param App\Libraries\Views   $views
     * @param App\Services\Entries  $entries
     * @param App\Services\Users    $users
     * @param App\Libraries\Session $session;
     */
    public function __construct(Request $request, Views $views, Entries $entries, Users $users, Session $session)
    {
        $this->request = $request;
        $this->views   = $views;
        $this->entries = $entries;
        $this->users   = $users;
        $this->session = $session;

        // Make the current user accessible to all views
        $this->views->addGlobalData([
            'user' => $this->users->getCurrentUser(),
        ]);
    }


    /**
     * Show all entries
     *
     * @return string
     */
    public function showEntries()
    {
        return $this->views->render('home', [
            'entries' => $this->entries->get(),
        ]);
    }


    /**
     * Get the new entry partial
     *
     * @return string
     */
    public function showNewEntry()
    {
        return $this->views->render('partials/entry-form', [
            'action' => '/entry/create',
            'title'  => 'Write a new entry',
        ]);
    }


    /**
     * Get the reply entry partial
     *
     * @return string
     */
    public function showReplyEntry()
    {
        $id = $this->request->get('id');

        $entry = $this->entries->byId($id);

        if (!$entry) {
            // Got no valid ID, let's show the new entry form instead
            return  $this->showNewEntry();
        }

        return $this->views->render('partials/entry-form', [
            'action'   => '/entry/reply',
            'title'    => 'Write your reply',
            'parentId' => $id,
        ]);
    }


    /**
     * Get the edit entry partial
     *
     * @return string
     */
    public function showEditEntry()
    {
        $id = $this->request->get('id');

        $entry = $this->entries->byId($id);

        if (!$entry || $entry['user_id'] != $this->users->getCurrentUser()['id']) {
            // Got no valid ID, let's show the new entry form instead
            return null;
        }

        return $this->views->render('partials/entry-form', [
            'action' => '/entry/update',
            'title'  => 'Edit your entry',
            'id'     => $id,
            'body'   => $entry['entry'],
        ]);
    }


    /**
     * Save new entry
     *
     * @return string
     */
    public function saveNewEntry()
    {
        $token = $this->request->post('token');

        return $this->add($token);
    }


    /**
     * Save new entry
     *
     * @return string
     */
    public function saveReplyEntry()
    {
        $token    = $this->request->post('token');
        $parentId = (int)$this->request->post('parent_id');

        return $this->add($token, $parentId);
    }


    /**
     * Save new entry
     *
     * @return string
     */
    public function updateEntry()
    {
        $token = $this->request->post('token');
        $id    = (int)$this->request->post('id');
        $body  = $this->request->post('body');

        $entry = $this->entries->byId($id);

        if (!$entry || $entry['user_id'] != $this->users->getCurrentUser()['id']) {
            // Got no valid ID, let's show the new entry form instead
            return $this->jsonResponse(null, 'You can only edit your own entries');
        }

        if ($this->entries->update($id, ['entry' => $body])) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(null, 'An unknown error occurred');
    }


    /**
     * Add a new entry or a reply
     *
     * @param  string $token
     * @param  int    $parentId
     * @return string
     */
    protected function add($token, $parentId = 0)
    {
        if (!$this->session->verifyCsrf('entry-form', $token)) {
            return $this->jsonResponse(null, 'Seems like the session has expired. Please reload the page and try again.');
        }

        $data = [
            'parent_id' => (int)$parentId,
            'entry'     => $this->request->post('body'),
            'user_id'   => (int)$this->users->getCurrentUser()['id'],
        ];


        $validate = $this->entries->validate($data);
        if ($validate !== true) {
            return $this->jsonResponse(null, $validate);
        }

        $entry = $this->entries->add($data);

        $view = $this->views->render('partials/entry', [
            'entry' => $entry
        ]);

        return $this->jsonResponse($view);
    }


    /**
     * Delete entry
     *
     * @return string
     */
    public function deleteEntry()
    {

        $id    = $this->request->post('id');
        $token = $this->request->post('token');

        if (!$this->session->verifyCsrf('delete-entry-token' . $id, $token)) {
            return $this->jsonResponse(null, 'Seems like the session has expired. Please reload the page and try again.');
        }

        $entry = $this->entries->byId($id);

        if (!$entry) {
            return $this->jsonResponse(null, 'Invalid entry id');
        }

        if ($entry['user_id'] != $this->users->getCurrentUser()['id']) {
            return $this->jsonResponse(null, 'You can only delete your own entries');
        }

        if ($this->entries->delete($id)) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(null, 'An unknown error occurred');
    }
}
