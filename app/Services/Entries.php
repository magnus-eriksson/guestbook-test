<?php namespace App\Services;

use App\Libraries\Database;

class Entries
{
    /**
     * @var Database
     */
    protected $db;

    /**
     * @var array
     */
    protected $sorted;


    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }


    /**
     * Add an entry
     *
     * @param array $data
     */
    public function add(array $data)
    {
        $data['created']  = date('Y-m-d H:i:s');
        $data['updated']  = date('Y-m-d H:i:s');

        $sql = "INSERT INTO entries (entry, user_id, parent_id, created, updated)
            VALUES (:entry, :user_id, :parent_id, :created, :updated)";

        $id = $this->db->insert($sql, $data);

        if (!$id) {
            return false;
        }

        return $this->byId($id);
    }


    /**
     * Update an entry
     *
     * @param  int    $id
     * @param  array  $data
     * @return array|false  If successful, return the updated record
     */
    public function update($id, array $data)
    {
        // Prepare the user data
        $data['updated'] = date('Y-m-d H:i:s');
        $data['id']      = $id;

        $sql = "UPDATE entries SET entry = :entry, updated = :updated WHERE id = :id";

        return $this->db->update($sql, $data)
            ? $this->byId($id)
            : false;
    }


    /**
     * Delete an entry
     *
     * @param  int     $id
     * @return boolean
     */
    public function delete($id)
    {
        $deleted = date('Y-m-d H:i:s');

        $sql = "UPDATE entries SET deleted = :deleted WHERE id = :id";
        $this->db->update($sql, [
            'deleted' => $deleted,
            'id'      => $id,
        ]);

        $entry = $this->byId($id);

        return !$entry || !is_null($entry['deleted']);
    }


    /**
     * Get all entries
     *
     * @return array
     */
    public function get()
    {
        if (is_null($this->sorted)) {
            $sql = "SELECT e.id, e.entry, u.name, e.user_id, e.parent_id, e.created, e.updated, e.deleted
                FROM entries e
                INNER JOIN users u ON u.id = e.user_id
                ORDER BY e.parent_id ASC, e.created DESC";

            $parents = [];

            // Group the entries by their parents
            foreach ($this->db->select($sql) as $entry) {
                $parents[$entry['parent_id']][] = $entry;
            }

            // Sort the array so we get the entries in the correct
            // hierarchically order
            $this->sorted = $this->sort($parents, 0);
        }

        return $this->sorted;
    }


    /**
     * Sort the entries in a hierarchy order and add level
     *
     * @param  integer $parentId
     * @param  array   $list
     * @param  integer $level
     * @return array
     */
    protected function sort(array &$parents, $parentId, $list = [], $level = 0, $parentName = null)
    {
        // Get the children and set the level so we can display them
        // nice and in a hierarchy.
        foreach ($parents[$parentId] ?? [] as $entry) {
            $entry['level']    = $level;
            $entry['reply_to'] = $parentName;
            $list[]            = $entry;
            $list              = $this->sort($parents, $entry['id'], $list, $level + 1, $entry['name']);
        }

        return $list;
    }


    /**
     * Get an entry by id
     *
     * @param  int   $id
     * @return array
     */
    public function byId($id)
    {
        $sql = "SELECT e.id, e.entry, e.user_id, u.name, e.parent_id, e.created, e.updated, e.deleted
            FROM entries e
            INNER JOIN users u ON u.id = e.user_id
            WHERE e.id = :id
            ORDER BY e.parent_id ASC, e.created DESC";

        return $this->db->select($sql, ['id' => $id], true);
    }


    /**
     * Validate an entry
     *
     * @param  array      $data
     * @return array|bool       True if success or array with errors
     */
    public function validate(array $data)
    {
        $errors = [];

        if (strlen(trim($data['entry'] ?? '')) < 5) {
            $errors[] = 'An entry must contain at least 5 characters';
        }

        if ($data['parent_id'] && !$this->byId($data['parent_id'])) {
            $errors[] = 'Invalid entry id';
        }

        if (array_key_exists('id', $data)) {
            if ($data['id'] && !$this->byId($data['id'])) {
                $errors[] = 'Invalid parent id';
            }
        }

        return $errors ?: true;
    }
}
