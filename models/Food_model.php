<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Food_model extends CI_Model {


public function getUser($username, $password)
{
        $result = $this->db->query("CALL sp_login('$username','$password')");
        $result =  $result->result_array();
        mysqli_next_result( $this->db->conn_id );
        
        return $result;
}



public function registerUser($lastname, $firstname, $middlename, $contactNo, $email, $username, $password, $ip)
{
        $result = $this->db->query("CALL sp_users('$lastname','$firstname','$middlename','$contactNo','$email','$username','$password', '$ip')");
        $result =  $result->result_array();
        mysqli_next_result( $this->db->conn_id );
        return $result;
}

public function login($username, $password, $ip) {
        
        $result = $this->db->query("CALL sp_login(
                '$username',
                '$password', 
                '$ip'
            );");
        $result =  $result->result_array();
        mysqli_next_result( $this->db->conn_id );
        // print_r($result);exit();
        return $result;
}

public function updateProfile($query){
        $result = $this->db->query("UPDATE user_details set $query");
        // print_r($query);
        return $result;
}

public function getList() {
        $result = $this->db->query("SELECT `user_details`.`id`,`user_details`.`fname`,`user_details`.`mname`,`user_details`.`lname`,`user_details`.`bdate`,`user_address`.`street`,`user_address`.`city`,`user_address`.`country`,`user_address`.`pcode`,`user_details`.`contactno`,`user_details`.`email`,`user_details`.`gender`, `user_details`.`status`, `user_category`.`description` FROM user_details 
        INNER JOIN `user_address` ON `user_address`.`id` = `user_details`.`address` INNER JOIN `user_category` ON `user_category`.`id` = `user_details`.`type`
        ");
        $result =  $result->result_array(); 
        return $result;
}

public function getMerchant() {
        $result = $this->db->query("SELECT * FROM merchant_account");
        $result =  $result->result_array(); 
        return $result;
}

public function sendEmail($username, $password) {
        $result = $this->db->query("CALL sp_check_email('$username', '$password')");
        $result =  $result->result_array();
        mysqli_next_result( $this->db->conn_id );
        // print_r($result[0]['email']);exit();
        if (count($result) > 0) {
                $params = array('username' => $username, 'password' => $password, 'ip' => $this->input->ip_address());
                $this->load->library('encryption');
                $config = array(
                'protocol'  => 'smtp',
                'smtp_host' => 'ssl://mail.upspheresolutions.com',
                'smtp_port' => 465,
                'smtp_user' => 'satisfood@upspheresolutions.com',
                'smtp_pass' => 'satisfood',
                'mailtype'  => 'html',
                'charset'   => 'utf-8'
                );
                $this->email->initialize($config);
                $this->email->set_mailtype("html");
                $this->email->set_newline("\r\n");
                //Email content
                $htmlContent = '<h1 style="color: #5e9ca0;">SATISFOOD IP VERIFICATION</h1>';
                $htmlContent .= '<label style="color: #5e9ca0;">please copy this link: <a href="http://ghd.qqt.mybluehost.me">'.base64_encode(json_encode($params)).'</a></label>';
                
                $this->email->to($result[0]['email']);
                $this->email->from('satisfood@upspheresolutions.com');
                $this->email->subject('NO REPLY!!');
                $this->email->message($htmlContent);
                //Send email
                $res =   $this->email->send();
                return $res;
        } else {
                print_r('Email not found');exit();
        }
    }



    public function verifyIp($password, $username, $ip) {
        $result = $this->db->query("CALL sp_manageIp('$password','$username','$ip')");
        $result =  $result->result_array();
        mysqli_next_result( $this->db->conn_id );
        return $result;
    }
// <!-- public function insert_entry()
// {
//         $this->title    = $_POST['title']; // please read the below note
//         $this->content  = $_POST['content'];
//         $this->date     = time();

//         $this->db->insert('entries', $this);
// }

// public function update_entry()
// {
//         $this->title    = $_POST['title'];
//         $this->content  = $_POST['content'];
//         $this->date     = time();

//         $this->db->update('entries', $this, array('id' => $_POST['id']));
// } -->

}