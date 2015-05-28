<?php
    ini_set("display_errors",1);
    require_once("./resources/query_functions.php");

    if(isset($_POST['function'])){
        $fun_name= $_POST['function'];
        unset($_POST['function']);
        echo $fun_name($_POST);
    }else
        echo '{"status":"error","message":"No Function Name", "error_num":"2"}';

    function check_registration_code($args){
        $res   = perform_query("SELECT * FROM `registration_codes` WHERE `registration_code`=? ","s", array($args['registration_code']));
        if(sizeof($res[0]))
            if($res[0]['used']=='1')
                return '{"status":"error","message":"Registration Code Used"}';
            else{
                return '{"status":"success","message":"Valid Code"}';
            }
        else
            return '{"status":"error","message":"Invalid Registration Code"}';
    }
    function check_used_email($args){
        $email = $args['email'];
        $res   = perform_query("SELECT * FROM `users` WHERE `email`=?","s", array($email));
        if(sizeof($res[0]))
            return '{"status":"error","message":"email already registered"}';
        else
            return '{"status":"success","message":"email not already registered"}';
        
    }
    function check_used_phone($args){
        $phone = $args['phone'];
        $res   = perform_query("SELECT * FROM `users` WHERE `phone`=?","s", array($phone));
        
        if(sizeof($res[0]))
            return '{"status":"error","message":"phone number already registered"}';
        else
            return '{"status":"success","message":"phone number not already registered"}';
    }
    function save_signup_info($args){
        
        $types= '';
        $values_array = array();
        $question_array = array();
        $phone = $args['phone'];
        

        /*CHECK PHONE*/
        $res   = perform_query("SELECT * FROM `registration_codes` WHERE `registration_code`=?","s", array($args['registration_code']));
        if(!sizeof($res[0]) || $res[0]['used'] == 1 )
            return '{"status":"error","message":"Invalid Registration Code"}';
        /*CHECK PHONE*/
        $res   = perform_query("SELECT * FROM `users` WHERE `phone`=?","s", array($args['phone']));
        if(sizeof($res[0]))
            return '{"status":"error","message":"phone number already registered"}';
        /*CHECK EMAIL*/
        $res   = perform_query("SELECT * FROM `users` WHERE `email`=?","s", array($args['email']));
        if(sizeof($res[0]))
            return '{"status":"error","message":"email already registered"}';
                
        if(($args['password1'] != $args['password2']))
            return '{"status":"error","message":"Passwords do not match"}';
        
        $args['password'] = $args['password1'];
        unset($args['password1']);
        unset($args['password2']);
        $args['signup_key']= get_signup_key();
        $keys = array_keys($args);
        
        foreach($args as $key=>$item){
            if($key == "password")
                $question_mark_arr[]= 'PASSWORD(?)';
            else
                $question_mark_arr[]= "?";

            $types_string.="s";
            $insert_vals[]= $item;
        }

        //error_log("signup_v2 #105 the args to save are: ". print_r($args,1));
        $insert_string = "INSERT INTO `users` (".implode(", ", array_keys($args) ).") VALUES (".implode(",", $question_mark_arr ).")";
        $res=perform_query($insert_string,$types_string, $insert_vals);
        $new_id = perform_query("SELECT `id` FROM `users` WHERE `phone` = ? LIMIT 1", "s", array($phone));
        generate_registration_codes($new_id[0]['id']);
        if( is_array($res)){
            perform_query("UPDATE `registration_codes` SET `used` = '1' WHERE `registration_code` = ? and `persistent` = 0 LIMIT 1","s", array($args['registration_code']));
            mail($args['email'], "Welcome To Endurance Leaders", "Hello ". $args['name']. " and welcome to the Endurance Leaders Family! \n\nWe are dedicated to helping athletes and business professionals engage with the world around them by building meaningful connections with people in their community.\n\n Click this link to finish your signup process and begin connecting! http://codeallthethings.com/eL/finish_signup/?code=".$args['signup_key']."\n\n-The eL Family");
            return '{"status":"success","message":"row inserted"}';
            
        }else 
            return '{"status":"error","message":"Could not insert row. error:'.print_r($res,1).'"}';
    }
    function get_signup_key(){
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for( $j = 0; $j < 20; $j++){
            do{
                $string = '';
                for ($i = 0; $i < 21; $i++) {
                    $string .= $characters[rand(0, strlen($characters) - 1)];
                }
                $res = perform_query("SELECT * FROM `users` WHERE `signup_key`=?","s", array($string));
            }while(sizeof($res));
            return $string;
            //perform_query("INSERT INTO `users` (`registration_code`,`user_id`, `used`) VALUES(?,?,'0')","ss",array($string, $user_id));
        }
    }
    function generate_registration_codes($user_id){
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for( $j = 0; $j < 6; $j++){
            do{
                $string = '';
                for ($i = 0; $i < 7; $i++) {
                    $string .= $characters[rand(0, strlen($characters) - 1)];
                }
                $res = perform_query("SELECT * FROM `registration_codes` WHERE `registration_code`=?","s", array($string));
            }while(sizeof($res));
            perform_query("INSERT INTO `registration_codes` (`registration_code`,`user_id`, `used`) VALUES(?,?,'0')","ss",array($string, $user_id));
        }
    }
    function login($args){
        $query = "SELECT * FROM `users` where `email`=? and `password`=PASSWORD(?)";
        $res   = perform_query($query, "ss", array($args['email'], $args['password']));
        if(sizeof($res[0])){
            $login_id    = substr(md5(rand()), 0, 7);
            $expire_time = time()+60*60*24*30;
            
            while(check_cookie($login_id)=="used"){
                $login_id = substr(md5(rand()), 0, 7);
            }

            $insert_cookie_query = "INSERT INTO `web_login` (`email`,`cookie_id`,`valid`,`user_id`) VALUES (?,?,'1',?)";
            perform_query($insert_cookie_query, "sss", array($args['email'],$login_id,$res[0]['id']));

            setcookie("endurance_leaders",$login_id, $expire_time, "/");
            //header("location:http://codeallthethings.com/eL/user_backend");
            return '{"status":"success","message":"logged_in"}';
        }else{
            return '{"status":"error","message":"Login error"}';
        }
    }
    function check_cookie($login_id){
        global $mysqli;
        $login_id = $mysqli->real_escape_string($login_id);
        $query = "SELECT * FROM `logged_in` where `cookie_id` = '$login_id'";
        $res = $mysqli->query($query);
        if($res->num_rows){
            return "used";
        }else{
            return "unused";
        }
    }
    function request_code($args){
        mail("niallsc@gmail.com", "Endurance Leaders Request", "This email: " . $args['email']. " has requested a registration code for eL.");
        mail($args['email'],"Thank you for your interest in eL", "Thank you for your interest in eL, we will email you when we have more requests available.");
        perform_query("INSERT INTO `registration_code_requests` (`email`) VALUES (?)", "s",array($args['email']));
        return '{"status":"success","message":"request_code requested"}';
    }

?>