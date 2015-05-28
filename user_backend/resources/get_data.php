<?php
    /**
     * Ajax requests for user data.
     */

    ini_set("display_errors",1);
    require_once("./core.php");
    if(isset($_POST['function'])){
        $fun_name= $_POST['function'];
        unset($_POST['function']);
        $_POST['user_data'] = web_logged_in();

        if(!$_POST['user_data']){
            echo '{"status":"error","message":"Not Logged In", "error_num":"1"}';
        }else{
            $o_get_user_data = new Get_User_Data();
            if (method_exists($o_get_user_data, $fun_name)) {

                error_log("method exists");
                echo $o_get_user_data->$fun_name($_POST);
            } else {
                error_log("method doesn't exist");
                echo '{"status":"error","message":"Method doesn\'t exist", "error_num":"1"}';
                //echo $o_get_user_data->$fun_name($_POST);
            } 
        }

    }else
        echo '{"status":"error","message":"No Function Name", "error_num":"2"}';

class Get_User_Data {
    function get_codes($args){
        $res = perform_query("SELECT * FROM `registration_codes` WHERE `user_id` = ? ","s",array($args['user_data']['id']));
        $ret_arr = array( "used_codes" => array(), "unused_codes" => array() );
        foreach($res as $code_row){
            if($code_row['used']=="1"){
                $res2 = perform_query("SELECT `name` FROM `users` WHERE `registration_code` = ? LIMIT 1","s",array($code_row['registration_code']));
                $code_row["name"]=$res2[0]['name'];
                $ret_arr['used_codes'][] = $code_row;
            }
            else
                $ret_arr['unused_codes'][] = $code_row;

        }
        return json_encode(array("status"=>"success","codes"=>json_encode($ret_arr)));
    }
    function get_profile_pages($args){
        $res = perform_query("SELECT * FROM `profile_pages` WHERE `disabled` = '0' ","",array(""));
        $user_info = $args['user_data']['profile'];
        error_log("the user info is: ". print_r($user_info,1));
        foreach($res as $key => $page){

            $res2 = perform_query("SELECT * FROM `profile_parts` WHERE `disabled` ='0' AND `page_id` = ? ","s", array($page['id']));
            foreach($res2 as $key2 => $row){
                //echo print_r($args['user_data']['profile']->$page['profile_name'],1);
                if($row['type'] == "select" && isset($user_info->$page['profile_name']->$row['name'])){
                    $res2[$key2]['selected'] = $user_info->$page['profile_name']->$row['name'];
                }else if($row['type'] == 'checkbox'){
                    $res2[$key2]['options'] = json_decode($row['options']);
                    //error_log("my profile is: ". print_r($args['user_data']['profile']->$page['profile_name'],1));
                    $res2[$key2]['options'] = $this->parse_options($res2[$key2]['options'], $user_info->$page['profile_name']);

                }else if( ($row['type'] == 'textarea' || $row['type'] == 'text') && isset($user_info->$page['profile_name']->$row['name'])){
                    $res2[$key2]['value'] = $user_info->$page['profile_name']->$row['name'];
                }
            }
            $res[$key]['page_data']= $res2;
        }
        return json_encode(array("status"=>"success","profile_pages"=>$res));
    }
    function parse_options($options, $profile_name){
        if($options){
            foreach($options as $idx => $box){
                $val = $box->value;
                if(isset($profile_name->$val)){
                    $options[$idx]->checked="1";
                }
                if(isset($box->triggers)){
                    if($box->triggers->type=="checkbox")
                        $options[$idx]->triggers->options = $this->parse_options($box->triggers->options, $profile_name);
                    else if($box->triggers->type=="textbox"){
                        $nam= $box->triggers->name;
                        if(isset($profile_name->$val)){
                            $options[$idx]->value=$profile_name->$val;

                        }if(isset($profile_name->$nam)){
                            $options[$idx]->triggers->value = $profile_name->$nam;
                        }
                    }
                }
            }
        }
        return $options;
    }

    /**
     * Looks for new possible connections for this user.
     * Only includes users within $args['user_data']['profile']->connection->max_distance_connection.
     * Only includes users not already connected to.
     * Order the connections as a combined function of distance and compatibility.
     * @param args The arguments passed in through the ajax interface, including the user_data arguments.
     * @return The possible new connections this user can make.
     */
    function get_connections($args){
        //lat lat long
        $conn = preg_replace("/[^0-9]/","",$args['user_data']['profile']->connection->max_distance_connection);

        // Gets all information about other users located near to this user.
        $query = "SELECT *, ( 3956 * acos( cos( radians(?) ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians(?) ) + sin( radians(?) ) * sin( radians( `lat` ) ) ) ) AS distance FROM `users` HAVING `id` != ? and `profile_complete` = '1' AND `distance` <= ? ORDER BY distance ASC";
        $local_users = perform_query($query, "sssss", array($args['user_data']['lat'],$args['user_data']['long'],$args['user_data']['lat'],$args['user_data']['id'], $conn));
        error_log(print_r($local_users,1));

        // Check that some amount of users were found.
        if(sizeof($local_users[0]) < 1){
            return json_encode(array("status"=>"failure","message"=>"There are no users in your area."));
        }

        // Get a list of other users not yet connected to and rank them by
        // location and compatibility.
        $ret_arr = [];
        foreach($local_users as $other){

            // Check that there is not yet a connection between the current user
            // and this $other user.
            $connections = perform_query("SELECT * FROM `connections` where `user_id` = ? and `connected_to` = ?","ss",array($args['user_data']['id'], $other['id']));
            error_log(print_r($connections, 1));
            if(sizeof($connections[0]) > 0){
                continue;
            }

            // Count the number of matched profile parts for this user.
            // A simple matching of prefered activities/frequences/etc.
            $profile =  json_decode($other['profile']);
            $matched_parts    = [];
            $num_matched      = 0;
            $num_not_matched  = 0;
            $serialized_user  = serialize_user_profile($args["user_data"]["profile"]);
            $serialized_other = serialize_user_profile($profile);
            foreach($serialized_user as $interest=>$interest_name) {
                if (isset($serialized_other[$interest])) {
                    $num_matched++;
                    $matched_parts[] = $interest_name;
                    unset($serialized_other[$interest]);
                } else {
                    $num_not_matched++;
                }
            }
            $num_not_matched += sizeof($serialized_other);
            error_log($num_matched . ":" . $num_not_matched);

            // Eliminate users that are too incompatible
            if($num_matched < 3) {
                continue;
            }

            // Add this user to the list of compatible users.
            $ret_arr[] = array(
                "user_id"=>$other['id'],
                "lat"=>$other['lat'],
                "long"=>$other['long'],
                "name"=>$other['name'],
                "matched_parts"=>$matched_parts,
                "percentage_matched"=> $num_matched/($num_matched + $num_not_matched));
        }

        // If the user doesn't have any matches let them know.
        if (sizeof($ret_arr) === 0) {
            return json_encode(array("status"=>"failure","message"=>"No compatible users."));
        }
        return json_encode(array("status"=>"success","matches"=>$ret_arr));
    }
    function update_location_for_user($args){

        if(isset($args['lat']) && isset($args['long']) && !empty($args['lat']) && !empty($args['long']) ){

            $res = perform_query("UPDATE `users` SET `lat` = ?, `long` =? WHERE `id` =? ","sss",array($args['lat'], $args['long'], $args['user_data']['id']));
            return json_encode(array("status"=>"success","message"=>"lat_long updated"));
        }else
            return json_encode(array("status"=>"error","message"=>"no lat long updated"));

    }
    function save_profile($args){
        $new_profile = [];
        $is_activity = false;
        $is_professional = false;
        $user_data = $args['user_data'];
        unset($args['user_data']);
        $to_save_prof_page = [];
        if(isset($args['profile_part'])){
            $prof_page = $args['profile_part'];
            unset($args['profile_part']);
            $user_data['profile']->$prof_page= $args;

        }else{
            return json_encode(array("status"=>"error","message"=>"NO profile page sent"));
        }
        $res = perform_query("UPDATE `users` SET `profile` = ? WHERE `id` = ? LIMIT 1", "ss",array(json_encode($user_data['profile']), $user_data['id']));

        $dumb = json_decode($this->get_profile_pages(array("user_data"=>web_logged_in())));
        return json_encode(array("status"=>"success","message"=>"profile saved", "profile_pages"=>$dumb->profile_pages));  //this is the worst ever im so sorry...
    }
    function logout($args){
        perform_query("UPDATE `web_login` SET `valid` = 0 where `email` = ? ", "s", array($args['user_data']['email']));
        return json_encode(array("status"=>"success","message"=>"logged_out"));
    }
}

?>