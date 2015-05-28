<?php
    if(isset($_GET['code'])){
        require_once("./../resources/query_functions.php");
        $res = perform_query("SELECT * FROM `users` WHERE `signup_key` = ? LIMIT 1","s", array($_GET['code']));
        if(sizeof($res[0])){
            if($res[0]['signup_complete'] != '1'){
                perform_query("UPDATE `users` SET `signup_complete` = 1  WHERE `signup_key` = ? LIMIT 1","s", array($_GET['code']));
                header( 'Location: http://codeallthethings.com/eL/?signup_success=1');
            }else
                header( 'Location: http://codeallthethings.com/eL/?signup_success=0&message=Error, Signup Already Completed');
        }else{
            echo "Error, Invalid Code";
            header( 'Location: http://codeallthethings.com/eL/?signup_success=0&message=Error, Invalid Code');
        }
    }else{
        header( 'Location: http://codeallthethings.com/eL/?signup_success=0&message=Error, No signup Key Sent');
    }

?>