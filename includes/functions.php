<?php

//Function to short the number of views or comments
function numberShort(int $num){
    if ($num > 999 && $num < 999999){
        $num = $num/1000;
        $num = round($num, 1);
        return $num.'k';
    } elseif ($num > 999999 && $num < 999999999){
        $num = $num/1000000;
        $num = round($num, 1);
        return $num.'M';
    }
    return $num;
}

//Function to return time from date difference
function timeIndex($date){

    date_default_timezone_set("Asia/Calcutta");
    
    $currentDate = Date('Y-m-d H:i:s');
    $currentDate = new Datetime($currentDate);
    $date = new Datetime($date);

    $interval = $currentDate->diff($date);

    $years = $interval->y;
    $months = $interval->m;
    $days = $interval->d;
    $hours = $interval->h;
    $minutes = $interval->i;
    $seconds = $interval->s;

    if ($years > 0){
        if ($years > 1){
            return $years. ' Years';
        } else {
            return $years. ' Year';
        }
    } elseif ($months > 0){
        if ($months > 1){
            return $months. ' Months';
        } else{
            return $months. ' Month';
        }
    } elseif ($days > 0){
        if ($days > 1){
            return $days. ' Days';
        } else{
            return $days. ' Day';
        }
    } elseif ($hours > 0){
        if ($hours > 1){
            return $hours. ' Hours';
        } else{
            return $hours. ' Hour';
        }
    } elseif ($minutes > 0){
        if ($minutes > 1){
            return $minutes. ' Minutes';
        } else{
            return $minutes. ' Minute';
        }
    } elseif ($seconds > 0){
        if ($seconds > 1){
            return $seconds. ' Seconds';
        } else{
            return $seconds. ' Second';
        }
    }

    return '1 Second ago';
}

function validData($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data =  htmlentities($data);
    $data = strip_tags($data);
    return $data;
}

//Function to create awesome URLs
function slugify($title){

    $slug = preg_replace('/[^a-z0-9-]+/', '-', strtolower($title));

    return $slug;
}