
function build_form(form, form_eles){
    
    var percent_complete = 0.0;
    for(var i = 0; i < form_eles.length; i++){
        
        if(form_eles[i].type == "select")
            form.appendChild(build_select(form_eles[i]));
        else if(form_eles[i].type == "submit")
            form.appendChild(build_submit(form_eles[i]));
        else if(form_eles[i].type == "checkbox"){
            form.appendChild(build_multi_checkbox(form_eles[i]));
        }else if(form_eles[i].type=="textarea"){
            form.appendChild(build_textarea(form_eles[i]));
        }
        else
            form.appendChild(build_text(form_eles[i]));
    }
    $(form).jqTransform();
}

function build_textarea(info){
    var extras = JSON.parse(info.extra);
    row = build_default_row(1);
    build_label(row, info.text);
    inp = document.createElement("textarea");
    inp.setAttribute("rows", extras.rows);
    inp.setAttribute("cols", extras.cols);
    inp.setAttribute("name",info.name);
    if(info.value){
        inp.value = info.value;
        inp.setAttribute("section_complete", "1");
    }
    inp.addEventListener("blur", info.validate);
    inp.setAttribute("placeholder",info.placeholder);
    inp.setAttribute("class",info.clas);
    row.childNodes[0].appendChild(inp);
    return row;
}
function build_text(info){
    row = build_default_row(1);
    inp = document.createElement("input");
    inp.setAttribute("type",info.type);

    inp.setAttribute("name",info.name);
    inp.addEventListener("blur", info.validate);
    if(info.id)
        inp.id = info.id;
    
    if(info.value){
        inp.value = info.value;
        inp.setAttribute("section_complete", "1");
    }
    inp.setAttribute("placeholder",info.placeholder);
    inp.setAttribute("class",info.clas);
    row.childNodes[0].appendChild(inp);
    if(info.add_element)
        if(info.add_element.type=="checkbox"){
            row.childNodes[0].appendChild(build_checkbox(info.add_element));
        }
    return row;
}
function build_multi_checkbox(info){
    var extras = {}
    if(info.extra)
        extras = JSON.parse(info.extra);
    var row = build_default_row(1);
    if( info.text)
        build_label(row, info.text);
    if(typeof(info.options)=="string")
        info.options = JSON.parse(info.options);
    if(info.type=="checkbox"){
        for(var i = 0; i < info.options.length; i++){
            row.childNodes[0].appendChild(build_checkbox(info.options[i]));
        }
    }else if( info.type=="textbox"){
        row.childNodes[0].appendChild(build_text(info));
    }
    return row;
}
function build_checkbox(info){
    var di = document.createElement("div");
        di.className = "check_box_container"
    var inp = document.createElement("input");
    inp.setAttribute("type","checkbox");
    if(info.id){
        inp.id = info.id;
    }
    if(inp.value)
        inp.setAttribute("name",info.value);
    inp.setAttribute("value",1);
    
    inp.addEventListener("change", info.change);
    if(info.checked){
        inp.setAttribute("checked", "1");
        inp.setAttribute("section_complete", "1");
    }
    di.appendChild(inp);
    var label = document.createElement('label')
        label.htmlFor = "id";
        label.appendChild(document.createTextNode(info.name));
        di.appendChild(label);
    if(info.triggers){
        var row   = build_multi_checkbox(info.triggers);
        var row_eles = row.childNodes[0].childNodes;
        //for (var i = 0; i < row_eles.length; i++){
        //    row.childNodes[0].childNodes[i].childNodes[0].name = row.childNodes[0].childNodes[i].childNodes[0].value;
        //}
        
        var table = build_hidden_table(info.triggers.title);
        if(info.checked)
            table.style.display="block";
        table.appendChild(row);
        inp.addEventListener("change", show_hide_trigged_options);
        di.appendChild(table);
    }
    return di;
}
function build_select(info){
    row = build_default_row(1);
    
    build_label(row,info.text);
    sel = document.createElement("select");
    sel.setAttribute("name",info.name);
    sel.setAttribute("class",info.clas);

    sel.addEventListener("blur", info.validate);
    if(typeof(info.options)=="string"){
        info.options= JSON.parse(info.options);

    }
    for(var i = 0; i < info.options.length; i ++ ){
        opt = document.createElement("option");
        opt.setAttribute("value", info.options[i].value);
        opt.innerHTML =info.options[i].name;
        sel.appendChild(opt);
    }
    if(info.selected){
        sel.value = info.selected;
        sel.setAttribute("section_complete", "1");
    }
    row.childNodes[0].appendChild(sel);
    return row;

}
function build_submit(info){
    row = build_default_row(1);
    inp = document.createElement("div");
    if(info.submit)
        inp.addEventListener("click", info.submit);
    else if(info.validating_function)
        inp.addEventListener("click", eval(info.validating_function));
    
    inp.setAttribute("class",info.clas);
    if(info.value)
        inp.innerHTML = info.value;
    else if(info.text)
        inp.innerHTML = info.text;
    row.childNodes[0].appendChild(inp);
    return row;
}
function build_hidden_table(title){
    tab = document.createElement("table");
    tab.className = "hidden_table";
    tr =document.createElement("tr");
    if(title){
        th = document.createElement("th");
        th.innerHTML = title;
        tr.appendChild(th)
    }
    tab.appendChild(tr);
    return tab;

}
function build_default_row(num_cols){
    tr = document.createElement("tr");
    for(i = 0; i < num_cols; i++){
        td = document.createElement("td");
        tr.appendChild(td);
    }
    return tr;
}
function show_hide_trigged_options(evt){
    if(evt.target.checked){ //Show the table
        evt.target.parentNode.parentNode.getElementsByClassName("hidden_table")[0].style.display="block"
    }else{  //hide the table
        evt.target.parentNode.parentNode.getElementsByClassName("hidden_table")[0].style.display="none"
    }
}

function modify_phone(ele){

    jQuery(function($){ 
            var dumb = document.getElementById("phone_num");
            if(ele.target.checked)
                $(dumb).mask("+99-9999-9999");
            else
                $(dumb).mask("(999) 999-9999");
        }
    );
}
function validate_text(ele){
    if(ele.target.value.length == 0 || !ele.target.value.match(/[a-z0-9]+$/i)){
        create_error(ele.target, "Name must be alpha numeric and greater than 0");
    }else
       remove_error(ele.target);
    
}
function validate_email(ele){
    if(ele.target.value.length == 0 || !ele.target.value.match(/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i)){
        create_error(ele.target, "Invalid Email");
    }else if(used_email(ele.target.value)){
        create_error(ele.target, "Email has already been registered");
    }else
        remove_error(ele.target);
    
}
function validate_phone(ele){
    var phoneno = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/; 
    var phoneno_int =/^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;
    var chk_box =document.getElementById('modify_phone');
    if(chk_box.checked && !ele.target.value.match(phoneno_int)){
        create_error(ele.target, "Invalid Phone Number");
    }else if(!chk_box.checked && !ele.target.value.match(phoneno)){
        create_error(ele.target, "Invalid Phone Number");
    }else if(used_phone(ele.target.value)){
        create_error(ele.target, "Phone Number Already Registered");
    }else{
        remove_error(ele.target);
    }
}
function validate_drop_down(ele){
    if(ele.target.value ==""){
        create_error(ele.target, "Please Select a gender Option");
    }else 
       remove_error(ele.target);
}
function validate_age(ele){
    if(!ele.target.value.match(/[0-9]+$/i) || (parseInt(ele.target.value) < 18 || parseInt(ele.target.value) > 120)){
        create_error(ele.target, "Age Must be between 18 and 120");
    }else 
       remove_error(ele.target);
    
}
function validate_password(ele){
    if(ele.target.value.length < 8){
        create_error(ele.target, "Password must be greater than 8 characters");
    }else if(ele.target.parentNode.parentNode.nextSibling.getElementsByClassName("text_inp")[0].value.length > 0  &&  ele.target.value != ele.target.parentNode.parentNode.nextSibling.getElementsByClassName("text_inp")[0].value){
        create_error(ele.target, "Passwords must match");
    }else 
       remove_error(ele.target);   
}
function validate_password_again(ele){
    if(ele.target.value.length < 8 || ele.target.value != ele.target.parentNode.parentNode.previousSibling.getElementsByClassName("text_inp")[0].value){
        create_error(ele.target, "Passwords must match");
    }else 
       remove_error(ele.target);   
}
function used_phone(phone){
    var res = do_ajax("function=check_used_phone&phone="+phone);
    console.log(res);
    if( res.status =="error")
        return true; 
    else if( res.status =='success')
        return false;
}
function used_email(email){
    var res = do_ajax("function=check_used_email&email="+email);
    if( res.status =="error")
        return true; 
    else if( res.status =='success')
        return false;
}
function remove_error(ele){
    if(ele.parentNode.parentNode.parentNode.parentNode.parentNode.previousElementSibling.getElementsByClassName("error").length > 0)
        ele.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.removeChild(ele.parentNode.parentNode.parentNode.parentNode.parentNode.previousElementSibling);
}
function create_error(ele, str){
    if(ele.parentNode.parentNode.parentNode.parentNode.parentNode.previousElementSibling.getElementsByClassName("error").length == 0){        
        tr = document.createElement("tr");
        td = document.createElement("td");

        error_div = document.createElement("div");
        error_div.setAttribute("class","error");
        error_div.innerHTML = str;
        td.appendChild(error_div);
        tr.appendChild(td);
        ele.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.insertBefore(tr, ele.parentNode.parentNode.parentNode.parentNode.parentNode);
        ele.setAttribute("error","1");
    }
}
function build_label(row, text){
    if(text){
        var label = document.createElement('label')
        label.htmlFor = "id";
        label.className = "page_label_big";
        label.appendChild(document.createTextNode(text));
        label.innerHTML ="<br>"+label.innerHTML+"<br><br>";
        row.childNodes[0].appendChild(label);
    }
}

function do_ajax(args){
    var URL = "./web_stuff.php";
    var return_val; 
    $.ajax({
        url: URL,
        type: "POST",
        data:args,
        async:false,
        success: function (json){
            var obj = JSON && JSON.parse(json) || $.parseJSON(json);
            return_val = obj;
        }
    });
    return return_val;
}