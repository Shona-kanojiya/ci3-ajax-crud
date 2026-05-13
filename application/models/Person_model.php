<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Person_model
 *
 * All DB interaction uses CodeIgniter's Query Builder so every
 * value is automatically escaped – no raw SQL strings.
 */
class Person_model extends CI_Model {

    protected $table = 'persons';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ----------------------------------------------------------------
    //  READ – paginated list
    // ----------------------------------------------------------------
    public function get_all($limit = 10, $offset = 0)
    {
        return $this->db
                    ->order_by('id', 'DESC')
                    ->get($this->table, (int)$limit, (int)$offset)
                    ->result();
    }

    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    // ----------------------------------------------------------------
    //  READ – single record
    // ----------------------------------------------------------------
    public function get_by_id($id)
    {
        return $this->db
                    ->where('id', (int)$id)
                    ->get($this->table)
                    ->row();
    }

    // ----------------------------------------------------------------
    //  CREATE
    // ----------------------------------------------------------------
    public function insert($data)
    {
        $this->db->insert($this->table, $this->_clean($data));
        return $this->db->insert_id();
    }

    // ----------------------------------------------------------------
    //  UPDATE
    // ----------------------------------------------------------------
    public function update($id, $data)
    {
        $this->db->where('id', (int)$id);
        return $this->db->update($this->table, $this->_clean($data));
    }

    // ----------------------------------------------------------------
    //  DELETE
    // ----------------------------------------------------------------
    public function delete($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->delete($this->table);
    }

    // ----------------------------------------------------------------
    //  Email uniqueness check (exclude own row on edit)
    // ----------------------------------------------------------------
    public function email_exists($email, $exclude_id = 0)
    {
        $this->db->where('email', $email);
        if ($exclude_id) {
            $this->db->where('id !=', (int)$exclude_id);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    // ----------------------------------------------------------------
    //  Whitelist the allowed columns before any write
    // ----------------------------------------------------------------
    private function _clean($data)
    {
        $allowed = ['name', 'email', 'mobile', 'gender', 'state'];
        return array_intersect_key($data, array_flip($allowed));
    }
}
