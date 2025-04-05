<?php
class Feedback_model extends CI_Model {

    public function save_feedback($data) {
        return $this->db->insert('feedback', $data);
    }

    public function get_feedback_by_employee_id($employee_id) {
        $this->db->where('employee_id', $employee_id);
        return $this->db->get('feedback')->result_array();
    }

    public function get_average_rating($employee_id) {
        $this->db->select('AVG(rate) as avg_rating');
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get('feedback')->row();
        return $query->avg_rating ? round($query->avg_rating, 2) : 0;
    }

    public function get_all_employees_average_rating() {
        $this->db->select('employee_id, AVG(rate) as avg_rating');
        $this->db->group_by('employee_id');
        return $this->db->get('feedback')->result_array();
    }
}