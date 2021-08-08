<?php
?>
<script>
var check = localStorage.getItem('DM');
if (check == 'dark'){
    document.documentElement.setAttribute('data-theme', 'dark');
    document.getElementById("dm").checked = true;
    document.getElementById("dml").checked = true;
}
else
if (check == 'light'){
    document.documentElement.setAttribute('data-theme', 'light');
    document.getElementById("dm").checked = false;
    document.getElementById("dml").checked = false;
}

var cb1 = document.getElementById('dml');
var cb2 = document.getElementById('dm');

$(document).ready(function(){
    $('input[name=darkMode]').click(function(){
        if(this.checked){
            document.documentElement.setAttribute('data-theme', 'dark');
            cb1.checked = true;
            cb2.checked = true;
            localStorage.setItem('DM', 'dark');
        }
        else{
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('DM', 'light');
            cb1.checked = false;
            cb2.checked = false;
        }
    });
});
</script>