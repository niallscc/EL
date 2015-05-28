var form_eles = [ 
    {
        "type":"text", 
        "name":"name",
        "validate":validate_text,
        "placeholder":"Full Name",
        "clas":"text_inp"

    },{
        "type":"text", 
        "name":"phone",
        "validate":validate_phone,
        "placeholder":"Phone Number",
        "clas":"text_inp",
        "id": "phone_num",
        "add_element":{
            "id":"modify_phone",
            "type":"checkbox",
            "change":modify_phone,
            "name":"International?"
        }

    },{
        "type":"email", 
        "name":"email",
        "validate":validate_email,
        "placeholder":"Email Address",
        "clas":"text_inp"

    },{
        "type":"select", 
        "name":"gender",
        "validate":validate_drop_down,
        "clas":"select_inp",
        "options":[
            {"name": "Select Your Gender", "value" :""},
            {"name": "Male", "value" :"male"},
            {"name": "Female", "value" :"female"},
            {"name": "Other", "value" :"other"},
            {"name": "Do Not Wish to Specify", "value" :"none"}
        ]

    },{
        "type":"number", 
        "name":"age",
        "validate":validate_age,
        "placeholder":"Age",
        "clas":"text_inp"

    },{
        "type":"password", 
        "name":"password1",
        "validate":validate_password,
        "placeholder":"Password",
        "clas":"text_inp"

    },{
        "type":"password", 
        "name":"password2",
        "validate":validate_password_again,
        "placeholder":"Password Again",
        "clas":"text_inp"

    },{
        "type":"submit", 
        "value":"Signup!",
        "submit":submit_form,
        "clas":"submit_btn"
    }
];
$( document ).ready(function() {
    document.getElementById("login_btn").addEventListener("click", login);
    document.getElementById("request_btn").addEventListener("click",show_request_btn);
});
function validate_registration_code(ele){
    var val = '';
    if(ele)
        val = ele;
    else
        val = document.getElementById("registration_code").value;
    res = do_ajax("function=check_registration_code&registration_code="+val);
    if(res.status == "success"){
        form_eles.push({"type":"hidden","name":"registration_code","value":val});
        build_form(document.getElementsByClassName("signup_form")[0], form_eles);
        jQuery(function($){ 
            $("#phone_num").mask("(999) 999-9999");
        });
        $(".registration_code_table").fadeOut( "slow", function() {
           $(".signup_screen").fadeIn("fast");
        });
    }else{
        alert(res.message);
    }
}
function request_code(ele){
    email = document.getElementById("request_code").value;
    if(email.length == 0 || !email.match(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i)){
        create_error(email, "Invalid Email");
    }else{
        res = do_ajax("function=request_code&email="+email);
        if(res.status=="success"){
            $(".request_code_table").fadeOut("fast",function(){
                $(".registration_code_table").fadeIn("slow",function(){
                    alert("Request Sent!");
                });
            });
        }
    }
}
function show_request_btn(){
    $(".registration_code_table").fadeOut("slow",function(){
        $(".request_code_table").fadeIn("fast");
    });
}
function submit_form(ele){
    var form = document.getElementById("form_signup");
    var serialized = $(form).serialize();
    console.log(serialized);
    var result = do_ajax("function=save_signup_info&"+serialized);
    if( result.status=='success'){
        $(".signup_form").fadeOut( "slow", function() {
           document.getElementsByClassName("signup_form")[0].innerHTML = "<div class = 'finished' >Signup Complete! <br><br> Check your email to finish the signup process.<br><br> If you don't see the email right away, check your spam.</div>";
           $(".signup_screen").fadeIn( "fast");
        });
    }else
        alert(result.message);  
}
function show_login(){
    $($(".button_td")[1]).css("background-color","#CFD8DC");
    $($(".button_td")[0]).css("background-color","white");
    if($(".registration_code_screen").css("display") != 'none'){
       $(".registration_code_screen").fadeOut("slow", function(){
            $(".login_screen").fadeIn("fast");
            
       });
        
    }
}
function show_signup(){
    $($(".button_td")[0]).css("background-color","#CFD8DC");
    $($(".button_td")[1]).css("background-color","white");
    if($(".login_screen").css("display") != 'none'){
       $(".login_screen").fadeOut("slow", function(){
            $(".registration_code_screen").fadeIn("fast")
            
       });
        
    }
}
function login(ele){
    var form = document.getElementById("login_form");
    var serialized = $(form).serialize();
    var result = do_ajax("function=login&"+serialized);
    if( result.status == 'success'){
        window.location = "http://codeallthethings.com/eL/user_backend/";
    }else
        alert(result.message);  
}