<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function add_admin($data) {
        return $this->db->insert('auth', $data) ? $this->db->insert_id() : false;
    }

    // Get profile data for the logged-in user
    public function get_profile($user_id) {
        $this->db->where('id', $user_id);
        $query = $this->db->get('auth');
        return $query->row_array();
    }

    // Update profile data
    public function update_profile($user_id, $data) {
        $this->db->where('id', $user_id);
        $this->db->update('auth', $data);
    }

    public function get_authorities() {
        $this->db->select('auth.*');
        $this->db->from('auth');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function delete($id) {
        $this->db->where('id', $id); 
        return $this->db->delete('auth'); 
    }
}
