var profile_pages = {};
var prev_states = [];
var can_connect = false;
$( document ).ready(function() {
    update_location();
    build_profile_pages(get_pages_and_content());
    build_codes_box(get_codes());
    document.getElementById("home_btn").addEventListener("click",show_feed);
    get_connections();

});
function update_location(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);

    } else {
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}
function showPosition(position) {
    //x.innerHTML = "Latitude: " + position.coords.latitude +
    //"<br>Longitude: " + position.coords.longitude;
    update_location_on_server(position.coords.latitude, position.coords.longitude);
}
function update_location_on_server(lat, long){
    res = do_ajax("function=update_location_for_user&lat="+lat+ "&long="+long);
    if(res.status == "error"){
        alert("No location available, please allow location settings on your browser");
        can_connect = true;
    }else{

        can_connect = true;
    }
}

function build_codes_box(codes){
    codes = JSON.parse(codes.codes);
    used = document.getElementsByClassName("used_codes_table")[0];
    unused = document.getElementsByClassName("unused_codes_table")[0];
    unused_row = build_default_row(codes.unused_codes.length);
    used_row = build_default_row(codes.used_codes.length);
    for (var i = 0; i< codes.unused_codes.length; i++){
        unused_row.childNodes[i].innerHTML = "<a class='shareable_code' href = mailto:?subject=Get%20Involved%20With%20Endurance%20Leaders&body=I%20would%20like%20to%20connect%20with%20you%20on%20Endurance%20Leaders%2C%20click%20this%20link%20to%20sign%20up%20http%3A%2F%2Fcodeallthethings.com%2FeL%2F%3Fregistration_code%3D"+codes.unused_codes[i].registration_code+">"+codes.unused_codes[i].registration_code+"</a>";
        unused_row.childNodes[i].className = "shareable_code";
    }
    for (var i = 0; i< codes.used_codes.length; i++){
        used_row.childNodes[i].innerHTML= codes.used_codes[i].name;
    }
    used.appendChild(used_row);
    unused.appendChild(unused_row);
}
function share_code(evt){

}
function build_default_row(num_cols){
    tr = document.createElement("tr");
    for(i = 0; i < num_cols; i++){
        td = document.createElement("td");
        td.className = "table_cell";
        tr.appendChild(td);
    }
    return tr;
}
function build_profile_pages(pages){
    profile_pages = pages.profile_pages;
    container = document.getElementsByClassName("profile_pages")[0];
    for(var i = 0; i < profile_pages.length; i++){
        var btn = document.createElement("div");
        btn.className = "profile_page";
        btn.setAttribute("page", profile_pages[i].profile_name);
        btn.setAttribute("page_index", i);
        btn.innerHTML = profile_pages[i].title;
        btn.addEventListener("click", open_profile_page);
        container.appendChild(btn);
    }
}
function go_back(ele){
    document.getElementsByClassName("main_content_container")[0].innerHTML = prev_states.pop();
    document.getElementsByClassName("back_btn")[0].addEventListener("click",go_back)

}
function open_profile_page(ele){

    page_data = profile_pages[parseInt(ele.target.getAttribute("page_index"))].page_data;
    page_data.push(JSON.parse('{"type":"hidden","name":"profile_part","value":"'+ele.target.getAttribute("page")+'"}'));
    container = document.getElementsByClassName("main_content_container")[0];

    form = document.createElement("form");
    form.setAttribute("name",ele.target.getAttribute("page"));

    table = document.createElement("table");
    table.className = "profile_page_content";
    form.appendChild(table);
    prev_states.push(container.innerHTML);
    container.innerHTML = '';
    container.appendChild(form);
    build_form(table, page_data);
    add_back_btn(container);
}
function add_back_btn(container){
    btn = document.createElement("div");
    btn.className ="back_btn";
    btn.innerHTML ="Back";
    btn.addEventListener("click", go_back);
    container.appendChild(btn);
}
function get_pages_and_content(){
    return do_ajax("function=get_profile_pages");
}
function get_codes(){
    return do_ajax("function=get_codes");
}
function show_feed(){
     window.location.reload()
     //document.getElementsByClassName("main_content_container")[0].innerHTML ='<div class = "coming_soon_box" style ="text-align:center; font-size:25pt; margin-top:200px;font-weight:100;">  MyAthleteBuddy Matching System Coming Soon!<br><br> Stay Tuned! </div>';
}
function save_profile(ele){
    var serialized = $(ele.target.parentNode.parentNode.parentNode.parentNode).serialize();
    res = do_ajax("function=save_profile&"+serialized);
    if(res.status == "success" ){
        profile_pages = res.profile_pages;
        alert("saved");
    }else{
        alert("Save Failed: "+ res.message);
    }
}
function get_connections(ele){
    res = do_ajax("function=get_connections");
    show_connections(res['matches']);

}
function show_connections(matches){
    var match_container = document.getElementsByClassName("new_matches")[0];
    if(matches){
        for (var i = 0; i < matches.length; i++){
            var word_container = document.createElement("div");
                word_container.className = "new_match";
                word_container.innerHTML = "Match: " + matches[i].name + "<br> Percentage Compatible: " + (matches[i].percentage_matched*100)+ "%";
                word_container.addEventListener("click", show_connection);
                match_container.appendChild(word_container);
        }
    }else{
         var word_container = document.createElement("div");
                word_container.className = "no_matches";
                word_container.innerHTML = " No new matches yet. Go outside!";
                match_container.appendChild(word_container);
    }

}
function show_connection(){

}
function accept_connection(ele){

}
function get_pending_connections(ele){

}
function deny_connection(ele){

}
function remove_connection(ele){

}
function logout(){
    do_ajax("function=logout");
    window.location = "http://codeallthethings.com/eL";
}
function do_ajax(args){
    var URL = "./resources/get_data.php";
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