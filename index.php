<?php
require_once("./resources/Mobile_Detect.php");
require_once("./user_backend/resources/core.php");
if(isset($_COOKIE["endurance_leaders"])) {
    $user_info = web_logged_in();
    if($user_info){
        header('Location: http://codeallthethings.com/eL/user_backend/') ; 
    }
}
?>

<html>
    <head>
        <title> Endurance Leaders Signup </title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <?php
        $detect = new Mobile_Detect;
 
        // Any mobile device (phones or tablets).
        if ( $detect->isMobile() ||isset($_GET['is_mobile'])) {
            echo '<link rel="stylesheet" href="./index_mobile.css" type="text/css">';
        }else{
             echo '<link rel="stylesheet" href="./index.css" type="text/css">';
        }
        ?>
        
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,200' rel='stylesheet' type='text/css'>
        
        <meta name="description" content="Endurance Leaders Connecting Communities">
        <meta name="keywords" content="">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <script src="http://code.jquery.com/jquery-1.9.0.js"></script>
        <script type='text/javascript' src='./maskedInput.js'></script>
        <link rel="stylesheet" href="./resources/jqtransformplugin/jqtransform.css" type="text/css">
        <link rel="stylesheet" href="./resources/vegas/vegas.min.css">
        <script type="text/javascript" src="./resources/jqtransformplugin/jquery.jqtransform.js"></script>
        <script src="./resources/vegas/vegas.min.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
        <script src="./resources/core.js" type="text/javascript"></script>
        <script src="./index.js" type="text/javascript"></script>
        <script type="text/javascript" src="./resources/scroller/js/lib/highlight.pack.js"></script>
        <script type="text/javascript" src="./resources/scroller/js/lib/modernizr.custom.min.js"></script>
        <script type="text/javascript" src="./resources/scroller/scrollmagic/uncompressed/ScrollMagic.js"></script>
        <script type="text/javascript" src="./resources/scroller/scrollmagic/uncompressed/plugins/debug.addIndicators.js"></script>
        <LINK REL="SHORTCUT ICON" HREF="/Images/allthethings.jpg">
    
    </head>
    <body>
        <?php
            if(isset($_GET['signup_success']) && $_GET['signup_success'] == 1 ){
                echo "<div class = 'signup_wrapper'> <div class ='signup_success'> Signup Complete! Time to login!</div></div>";
            }else if(isset($_GET['signup_success']) && $_GET['signup_success'] == 0 ){
                echo "<div class = 'signup_wrapper'><div class ='signup_failed'> Activation Error! ".$_GET['message']."</div></div>";
            }
        ?>
        <div class = "main_container">
            <div class = 'login_signup_container'>
                <table class = "dumb_table" style = "width:600px;background-color:white; color:#616161">
                <tr><td class = "button_td" style = "background-color:white;" onclick = "show_login()">LOGIN</td>
                <td class = "button_td" onclick="show_signup()">SIGN UP</td></tr>
                </table>
            </div>
            <div class = "login_screen ">
                <form name ="login" id = "login_form">
                    <table class ="login_table">

                        <tr>
                            <td>
                                <input id ="email" class = 'text_inp'type = "text" placeholder="email" name = "email">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input id ="pass" class = 'text_inp'type = "password" placeholder="password" name = "password">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br><div class ="submit_btn" id="login_btn">Login</div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class = "registration_code_screen" style ="display:none">
                <table class = "registration_code_table">
                    <tr><th>Get Involved with Endurance Leaders</th></tr>
                    <tr>
                        <td>
                        
                            <input id ="registration_code" class = 'text_inp' type = "text" placeholder="Enter Your Registration Code" value = "<?php echo $_GET['registration_code'];?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br><div class ="submit_btn" onclick = "validate_registration_code()">Register</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class = "registration_code_request" id = "request_btn"> Request A Registration Code </div>
                        </td>
                    </tr>
                </table>
                <table class = "request_code_table" style = "display:none">
                    <tr>
                        <td><input id ="request_code" type = "text" placeholder="Enter Your Email">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br><div class ="submit_btn" onclick = "request_code(this)">Request Code</div>
                        </td>
                    </tr>
                </table>
                <div class = "signup_screen" style = "display:none" >
               <form id = "form_signup">
                   <table class ="signup_form"> 
                   </table>
                </form>
            </div>
            </div>
        </div>
        
    </body>
    <script>
        $('body').vegas({
            delay: 8000,
            shuffle: true,
            transition: 'slideDown2',
            transitionDuration: 3000,
            slides: [
                { src: './images/cyclists.jpg' },
                { src: './images/runner.jpg' },
                { src: './images/swimmer.jpg' },
                { src: './images/climbing.jpg' }
            ]
        });
    </script>
</html>
