<?php
require_once("./resources/core.php");
if(!isset($_COOKIE["endurance_leaders"])) {
   echo "error_ no cookie";
   //echo print_r($_COOKIE,1);
   //header( 'Location: http://codeallthethings.com/eL' ) ; 
} else {
    $user_info = web_logged_in();
    if(!$user_info){
        header('Location: http://codeallthethings.com/eL') ; 
    }
}
?>

<html>
    <head>
        <title> Endurance Leaders </title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <link rel="stylesheet" href="./../resources/jqtransformplugin/jqtransform.css" type="text/css">
        <link rel="stylesheet" href="./index.css" type="text/css">
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,200' rel='stylesheet' type='text/css'>
          <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

        <meta name="description" content="Endurance Leaders Connecting Communities">
        <meta name="keywords" content="">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <script src="http://code.jquery.com/jquery-1.9.0.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
        <script type='text/javascript' src='./../maskedInput.js'></script>
        <script src="./../resources/core.js" type="text/javascript"></script>
        <script src="./index.js" type="text/javascript"></script>
        <script type="text/javascript" src="./../resources/jqtransformplugin/jquery.jqtransform.js"></script>
        <LINK REL="SHORTCUT ICON" HREF="/Images/allthethings.jpg"></LINK>
    
    </head>
    <body>
        <div class ='header'>
            <div class = 'toggle_btn ' onclick = '$( ".left_content_bar" ).slideToggle( "fast" );'> <div style ="margin-left:50px;margin-top:7px" >Menu</div></div>
            <div class = 'header_items' onclick = "logout()">Logout</div>
        </div>
        <div class = 'left_content_bar'>
            <div class = "content_bar_link" id = "home_btn" onclick = "show_feed()"> Home </div>
            <div class = 'profile'>
                <div class = 'content_bar_text'>  Edit your profile: </div>
                <div class = 'profile_pages'>

                </div>
            </div>
            <div class = "content_bar_item unused_codes">
               <div class = 'content_bar_text'> Unused Registration Codes</div>
               <table class="unused_codes_table">
               
               </table>
            </div>
            <div class = "content_bar_item used_codes">
               <div class = 'content_bar_text'> Used Registration Codes</div>
               <table class="used_codes_table">

               </table>
            </div>

        </div>
        <div class="main_content">
            
            <div class = 'main_content_container'>
                
                <div class = 'new_matches'>
                    <div class = "new_matches_header"> New Matches </div>
                    <table style =" border-spacing: 10px; border-collapse: separate;"><tr class = "new_matches_container"></tr></table>
                </div>
                <div class = 'pending_requests'>
                    <div class = 'pending_requests_header'>Pending Connection Requests</div>
                     <table style =" border-spacing: 10px; border-collapse: separate;"><tr class = "new_matches_container"><td class='pending_match'>No Pending Connection Requests</td></tr></table>
                </div>

            </div>
        </div>
    </body>
</html>