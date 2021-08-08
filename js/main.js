


//Function for countdown
var timeLeft = 5;
var timer = setInterval(function(){
    document.getElementById("time-left").innerHTML = timeLeft;
    timeLeft--;
    if (timeLeft<0){
        clearInterval(timer);
    }
}, 1000);