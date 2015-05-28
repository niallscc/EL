<?php
    require_once(dirname(__FILE__)."/../../resources/query_functions.php");
    
    function web_logged_in(){
        $res = perform_query("SELECT * FROM `web_login` WHERE `cookie_id` = ? AND `valid` = '1' ", "s",array($_COOKIE['endurance_leaders']));
        //echo print_r($res,1);
        if(sizeof($res[0]) && isset($res[0]['user_id']) && !empty($res[0]['user_id'])){
            $res2 = perform_query("SELECT * FROM `users` where `id`=? LIMIT 1", "s", array($res[0]['user_id']));
            if(sizeof($res2[0]['profile']))
                $res2[0]['profile'] = json_decode($res2[0]['profile']); 
            else
                $res2[0]['profile']=[];
            return $res2[0];
        }else{
            return false;
        }
    }

?>