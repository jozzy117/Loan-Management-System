
<?php
    include 'headers/loanheader.php';
    require 'config.php';

    $loanid = $_COOKIE["lid"];

    $sql = $conn->query("SELECT * FROM loans where loanid = '$loanid'");
        if($sql->num_rows>0){
            while($row = $sql->fetch_assoc()){
                $loan_amount = $row["loan_amount"];
                $exptDaily = $row["exptDaily"];
                $createdtime = $row["createdtime"];
                $dueDate = $row["dueDate"];
                $gphone = $row["gnumber"];
                $glname = $row["glname"];
                $gmname = $row["gmname"];
                $gfname = $row["gfname"];
                $gaddress = $row["gaddress"];
                $gidcard = 'uploads/g/'. $row["gidcard"];
                $gpassport = 'uploads/g/'. $row["gpassport"];
            }
        }else{
            echo "No record found";
        }

    $sql = $conn->query("SELECT payment_amount FROM payments where loanid = '$loanid'");
        if($sql->num_rows>0){
            $paid = 0;
            while($row = $sql->fetch_assoc()){
                $paid += $row['payment_amount'];
            }
                $disablePay = $paid >= $loan_amount;
        }

    $expired = strtotime($dueDate);
    $ctime = strtotime($createdtime);
    $today = strtotime(date('Y-m-d'));
    $cannotPay = $today<=$ctime;


?>

    <div class="container">
                    <form name="newLoan" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>">
                        <h1>Loan Details</h1><br>
                        <div class="row" id="info">
                            <div class="col-md-5 basic">
                                    <label for="lname" id="lbllast_name">Loan Amount (NGN)</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="number" name="loanAmount" id="loanAmount" value="<?php echo "$loan_amount";?>" disabled/><br><br>
                                    <label for="lname" id="lbllast_name">Expt Daily Pmt (NGN)</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="number" name="exptDaily" id="exptDaily" value="<?php echo "$exptDaily";?>" disabled/><br><br>
                                    <label for="lname" id="lbllast_name">Created Date</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="createdate" id="createdate" value="<?php echo "$createdtime";?>" disabled/><br><br>
                                    <label for="lname" id="lbllast_name">Due Date</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="duedate" id="duedate" value="<?php echo "$dueDate";?>" disabled/><br><br>
                                   
                            </div>
                            <div class="col-md-5 pay">
                                    <h4>New Payment</h4>
                                    <label for="phone" id="lblamount">Amount</label>
                                    <input class="box" type="number" name="payment_amount" id="payment_amount" <?php if($disablePay || $cannotPay) echo 'disabled="disabled"'?> required/><br><br>
                                    <input type="button" id="cancel" name="cancel" style="margin:5px;" value="Exit" onclick="history.go(-1)"/>
                                    <input type="submit" id="submit" name="submit" style="margin:5px;" value="Make Payment" <?php if($disablePay || $cannotPay) echo 'disabled="disabled"'?>/><br>
                            </div>
                        </div>
                        <div class="row" id="ginfo">
                            <div class="col-md-5 guarantor">
                                    <h4>Guarantor Info</h4>
                                    <label for="phone" id="lblphone">Phone Number</label>
                                    <input class="box" type="number" name="gphone" id="gphone" value="<?php echo "$gphone";?>" disabled/><br><br>
                                    <label for="lname" id="lbllast_name">Last Name</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="glname" id="glname"  value="<?php echo "$glname";?>" disabled/><br><br>
                                    <label for="mname" id="lblmiddle_name">Middle Name</label>
                                    &nbsp;&nbsp;&nbsp;<input class="box" type="text" name="gmname" id="gmname"  value="<?php echo "$gmname";?>" disabled/><br><br>
                                    <label for="fname" id="lblfirst_name">First Name</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="gfname" id="gfname"  value="<?php echo "$gfname";?>" disabled/><br><br>
                                    <label for="address" id="lbladdress">Address</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="gaddress" id="gaddress"  value="<?php echo "$gaddress";?>" disabled/><br><br>
                            </div>

                            <div class="col-md-2 loanfile">
                                <h4>Uploads</h4>
                                <label for="file"> Passport</label>
                                <img src="<?php echo "$gpassport";?>" alt="" width ="150" height="150">
                                <label for="file" id="lblfile"> ID Card</label>
                                <img src="<?php echo "$gidcard";?>" alt="" width ="150" height="150">
                                <br>
                            </div>
                        </div>
                    </form>
                    <form method="post" action="paymentSchedule.php">
                                    <input type="submit" id="submit" name="submit" style="margin:5px;" value="Payment Schedule"/><br>
                                    </form>

                </div>

                <div class="row" id="newLoan">
                    
                </div>

                <div class="row" id="loan">
                    
                </div>
        </div>

</body>
</html>

<?php

   
if(isset($_POST['submit'])){
    $sql = $conn->query("SELECT expyDaily FROM loans where loanid = '$loanid'");
    if($sql->num_rows>0){
        while($row = $sql->fetch_assoc()){
            $exptDaily = $row["exptDaily"];
        }
    }else{
        echo "No record found";
    }
   $pament_amount = $_POST['payment_amount'] ;
   $payment_date = date('Y-m-d');
   $payment_status = "paid";
   $overdue = $pament_amount - $exptDaily;
   
        if($today < $expired){
            $sql = $conn->query("UPDATE payments SET payment_amount = '$pament_amount',createdtime = '$payment_date',payment_status = '$payment_status', overdue = '$overdue' WHERE (loanid ='$loanid' and exptpaydate ='$payment_date')");
            if ($sql){
                echo'<script type="text/javascript">';
                echo'   alert("Payment Successful");';
                echo'   window.location = "http://localhost/loanstar/clientDetails.php";';
                echo'</script>';
            }
        
        }else{
            $sql = $conn->query("INSERT INTO payments (loanid,payment_amount,createdtime,payment_status,overdue,exptpaydate) VALUES ('$loanid','$pament_amount','$payment_date','$payment_status','$pament_amount','$dueDate')");
            echo'<script type="text/javascript">';
            echo'   alert("Payment Successful");';
            echo'   window.location = "http://localhost/loanstar/clientDetails.php";';
            echo'</script>';
        }

}


?>

