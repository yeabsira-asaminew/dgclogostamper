<?php
class Login_model extends CI_Model {

    public function validate($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('auth');
        return $query->row_array();
    }

    public function update_last_login($user_id) {
        $this->db->where('id', $user_id);
        $this->db->update('auth', ['last_login' => date('Y-m-d H:i:s')]);
    }
    
}