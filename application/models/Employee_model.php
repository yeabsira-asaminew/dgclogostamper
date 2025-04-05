<?php
class Employee_model extends CI_Model {

    public function add($data)
    {
        $this->db->insert('employee', $data);
        return $this->db->insert_id();
    }
    
    public function get_all_employees()
    {
        $this->db->select('employee.*');
        $this->db->from('employee');
        // $this->db->where('employee.status', '1');
        $query = $this->db->get();
        return $query->result_array();
    }

    // only active=1 employees are fetched
    public function get_employees($search = '', $sort_by = 'id', $sort_order = 'asc', $per_page = 10, $page = 0) {
        $this->db->select('id, fname, mname, lname, sex, dept, position');
        $this->db->from('employee');
        $this->db->where('employee.status', '1');

        if ($search) {
            $this->db->like('fname', $search);
            $this->db->or_like('mname', $search);
            $this->db->or_like('lname', $search);
            $this->db->or_like('sex', $search);
            $this->db->or_like('dept', $search);
            $this->db->or_like('position', $search);
        }

        $this->db->order_by($sort_by, $sort_order);
        $this->db->limit($per_page, $page);
        return $this->db->get()->result_array();
    }

    public function get_employees_count($search = '', $per_page = 10) {
        $this->db->from('employee');
        $this->db->where('employee.status', '1');
        if ($search) {
            $this->db->like('fname', $search);
            $this->db->or_like('mname', $search);
            $this->db->or_like('lname', $search);
            $this->db->or_like('sex', $search);
            $this->db->or_like('dept', $search);
            $this->db->or_like('position', $search);
        }
        return $this->db->count_all_results();
    }

     // only inactive=0 employees are fetched
     public function get_inactive_employees($search = '', $sort_by = 'id', $sort_order = 'asc', $per_page = 10, $page = 0) {
        $this->db->select('id, fname, mname, lname, sex, dept, position');
        $this->db->from('employee');
        $this->db->where('employee.status', '0');

        if ($search) {
            $this->db->like('fname', $search);
            $this->db->or_like('mname', $search);
            $this->db->or_like('lname', $search);
            $this->db->or_like('sex', $search);
            $this->db->or_like('dept', $search);
            $this->db->or_like('position', $search);
        }

        $this->db->order_by($sort_by, $sort_order);
        $this->db->limit($per_page, $page);
        return $this->db->get()->result_array();
    }

    public function get_inactive_employees_count($search = '', $per_page = 10) {
        $this->db->from('employee');
        $this->db->where('employee.status', '0');
        if ($search) {
            $this->db->like('fname', $search);
            $this->db->or_like('mname', $search);
            $this->db->or_like('lname', $search);
            $this->db->or_like('sex', $search);
            $this->db->or_like('dept', $search);
            $this->db->or_like('position', $search);
        }
        return $this->db->count_all_results();
    }

    public function update_employee($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('employee', $data);
    }
    

    public function deactivate_employee($id, $active) {
    $this->db->set([
        'status' => $active,
        'qr_code' => NULL // Set qr_code to NULL (or '' for an empty string)
    ])
    ->where('id', $id)
    ->update('employee');

    return $this->db->affected_rows() > 0;
   }

public function activate_employee($id, $active){
    $this->db->set('status', $active)
    ->where('id', $id)
    ->update('employee');

    return $this->db->affected_rows() > 0;
}

    public function get_employee_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('employee');
        return $query->row_array();
    }

    public function save_feedback($data) {
        $this->db->insert('feedback', $data);
    }

    public function update_qr_code($employee_id, $qr_code) {
        $this->db->where('id', $employee_id);
        return $this->db->update('employee', ['qr_code' => $qr_code]);
    }
    public function get_employee_by_id_row($employee_id) {
        return $this->db->get_where('employee', ['id' => $employee_id])->row();
    }


    public function has_qr($id)
    {
        $this->db->where('id', $id);
        $this->db->where('qr_code IS NOT NULL'); 
        
        $query = $this->db->get('employee');
        return $query->num_rows() > 0;
    }
    

      // for dashboard
      public function get_employee_count_by_gender()
      {
          $this->db->select('COUNT(employee.id) as count');
          $this->db->from('employee');
          $this->db->group_by('employee.sex');
  
          $query = $this->db->get();
          return $query->result_array();
      }
}