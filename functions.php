<?php

//generate unique loan id
function generateLoanid(){
    require 'config.php'; //include database connection file
    $pre = "LOAN-";
    do {
        $random_number = mt_rand(0001,9999); 
        $date = date('ymdHis');
        $ref_id = $pre.$date.$random_number;
        //check if payment reference generated exists in the database
        $sql= "SELECT * FROM loans WHERE loanid='$ref_id'";
        $res = mysqli_query($conn, $sql);
        //repeat the loop while payment reference generated already exists in the database 
    } while (mysqli_num_rows($res)>0);      
    
    return $ref_id;  
}

//function to generate loan due date
function duedate($start){
    $numberofdays = 20;
    $d = new DateTime($start);
    $t = $d->getTimestamp();
   
    for($i=0; $i<$numberofdays; $i++){
        //add 1 day to timestamp; 86400s = 1 day
        $addDay = 86400;
        //get what day it is next day
        $nextDay = date('w', ($t+$addDay));
        //if it is saturday or sunday get $i -1
       if($nextDay == 0 || $nextDay == 6){
            $i--;
        }
        //modify timestamp, add 1 day
        $t = $t+$addDay;
    }
    $d->setTimestamp($t);
    $enddate = $d->format('Y-m-d');
    return $enddate;
}

//function to calculate expected daily payment of loan
function dailyPayment($amount){
    $payment = $amount/20;
    return $payment;
}

function getStartAndEndDate($week, $year) {
  $dateTime = new DateTime();
  $dateTime->setISODate($year, $week);
  $result['start_date'] = $dateTime->format('Y/m/d');
  $dateTime->modify('+6 days');
  $result['end_date'] = $dateTime->format('Y/m/d');
  return $result;
}



?>
