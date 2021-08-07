function users_assets_to_json(){
checkboxes = document.querySelectorAll('input[name="users_assets[]"]');
var users_assets=[];
checkboxes.forEach(e => { 
    if (e.checked){
    users_assets.push(e.value);
    
    }
})

document.getElementById("users_assets_json").value=JSON.stringify(users_assets);

}

function invert_check(){

  var checkboxes = document.querySelectorAll('input[type="checkbox"]');
  for (var checkbox of checkboxes) {
    if (checkbox.checked)
    checkbox.checked = false;
    else
    checkbox.checked=true;
    
  }
}

function check_uncheck_all(){
    var checked=false;
  var checkboxes = document.querySelectorAll('input[type="checkbox"]');
  if (checkboxes[0].checked)
  checked=true;
  for (var checkbox of checkboxes) {
    if (checked)
    checkbox.checked = false;
    else
    checkbox.checked=true;
    
  }
}

