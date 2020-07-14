<?php
require 'config.php';
include 'headers/header.php';

session_start();


$client_id = $_COOKIE["cliid"];
$_SESSION['clientID'] = $client_id;

if (isset($client_id)){
    $paid = 0;
    $overdue = 0;
    $todayPay = 0;
    $sql = $conn->query("SELECT * FROM clients where id = $client_id");
        if($sql->num_rows>0){
            while($row = $sql->fetch_assoc()){
                $lastName = $row["last_name"];
                $middleName = $row["middle_name"];
                $firstName = $row["first_name"];
                $address = $row["client_address"];
                $phone = $row["phone"];
                $acctNo = $row["account_number"];
                $bvn = $row["bvn"];
                $bank = $row["bank_name"];
                $id_card = 'uploads/c/'. $row["id_card"];
                $passport = 'uploads/c/'. $row["passport"];
                $client_name = $row["last_name"]. " " . $row["middle_name"]. " " . $row["first_name"];
            }
        }else{
            echo "No record found";
        }

?>

<html>
    <head>
        <link href="assets/css/table.css" rel="stylesheet">
    </head>
        <div class="container">
                <div class="row" id="info">
                    <form>
                    <h1>Client Information</h1><br>
                        <div class="col-md-5 basic">
                            <h4>Basic Info</h4>
                                <label for="lname" id="lbllast_name">Last Name</label>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="lname" id="lname" value="<?php echo "$lastName";?>" disabled/><br><br>
                                <label for="mname" id="lblmiddle_name">Middle Name</label>
                                &nbsp;&nbsp;&nbsp;<input class="box" type="text" name="mname" id="mname" value="<?php echo "$middleName";?>"disabled/><br><br>
                                <label for="fname" id="lblfirst_name">First Name</label>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="fname" id="fname" value="<?php echo "$firstName";?>"disabled/><br><br>
                                <label for="address" id="lbladdress">Address</label>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="address" id="address" value="<?php echo "$address";?>"disabled/><br><br>
                                <label for="phone" id="lblphone">Phone Number</label>
                                <input class="box" type="text" name="phone" id="phone" value="<?php echo "$phone";?>"disabled/><br><br>
                        </div>
                        <div class="col-md-5 acct">
                            <h4>Account Info</h4>
                                <label for="acctNo" id="lblacct_no">Acct No</label>
                                &nbsp;&nbsp;<input class="box" type="text" name="acctNo" id="acctNo" value="<?php echo "$acctNo";?>"disabled/><br><br>
                                <label for="bvn" id="lblbvn">BVN No</label>
                                &nbsp;<input class="box" type="text" name="bvn" id="bvn" value="<?php echo "$bvn";?>"disabled/><br><br>
                                <label for="bankName" id="lblbank_name">Bank</label>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="bankName" id="bankName" value="<?php echo"$bank";?>"disabled/><br>
                        </div>
                       

                        <div class="col-md-2 file">
                            <h4>Uploads</h4>
                                <label for="file"> Passport</label>
                                <img src="<?php echo "$passport";?>" alt="" width ="150" height="150">
                                <label for="file" id="lblfile"> ID Card</label>
                                <img src="<?php echo "$id_card";?>" alt="" width ="150" height="150">
                                <br>
                        </div>
                      
                    </form>

                </div>

                <div class="row" id="newLoan">
                    <form action="newLoan.php" method="post" name="form1" enctype="multipart/form-data">
                    <input type="hidden" id="clientId" name="clientId" value ="<?php echo "$client_id";?>">
                    <input type="hidden" id="clientName" name="clientName" value ="<?php echo "$client_name";?>">
                    <input type="submit" id="new_loan" name="new_loan" style="margin:5px;" value="New Loan"/><br>
                    </form>
                </div>

                <div class="row" id="loan">
                    <div class="table-responsive">
                        <table class="table table-hover" id="loan">
                            <tr>
                                <th>LOAN-ID</th>
                                <th>AMOUNT</th>
                                <th>PAID</th>
                                <th>BALANCE</th>
                                <th>OVER-DUE</th>
                            </tr>
                                <?php
                                $sql = $conn->query("SELECT loanid,loan_amount FROM loans where clientid = $client_id");
                                if($sql->num_rows > 0){
                                    while($row = $sql->fetch_assoc()){
                                    echo "<tr>";
                                        echo "<td>" . "<a href='loanDetails.php'>". $row["loanid"] . "</a>"."</td>";
                                        echo "<td>" . $row["loan_amount"] ."</td>";
                                        $loanid = $row["loanid"];
                                        $loanamt = $row["loan_amount"];
                                        $res = $conn->query("SELECT * FROM payments WHERE loanid = '$loanid'");
                                        if($res->num_rows>0){
                                            while($result = $res->fetch_assoc()){
                                                $paid = $paid + $result["payment_amount"];
                                               
                                            }
                                            $db = $conn->query("SELECT overdue FROM payments WHERE(loanid = '$loanid' and exptpaydate < CURDATE())");
                                            if($db->num_rows>0){
                                                while($row = $db->fetch_assoc()){
                                                    $overdue = $overdue + $row["overdue"];
                                                }
                                            }
                                            $dbql = $conn->query("SELECT overdue,payment_amount,exptdailypmt FROM payments WHERE(loanid = '$loanid' and exptpaydate = CURDATE())");
                                            if($dbql->num_rows>0){
                                                while($row = $dbql->fetch_assoc()){
                                                    $todayPay = $row["payment_amount"];
                                                    $todayexpt = $row["exptdailypmt"];
                                                    if($todayPay > 0){
                                                        $overdue = ($overdue + $todayPay)-$todayexpt;
                                                    }
                                                }
                                            }
                                            
                                            $bal = $loanamt - $paid;
                                            echo "<td>". $paid. "</td>" ;
                                            echo "<td>". $bal. "</td>" ;
                                            echo "<td>". $overdue. "</td>" ;
                                            $paid = 0;
                                            $bal = 0;
                                            $overdue = 0;
                                            
                                        }
                                        echo "</tr>";
                                    }
            
                                }
                                $sql->close();
                                ?>
                        </table>
                    </div>
                </div>
        </div>

    <script>

        function createCookie(name,value){
            document.cookie = escape(name) + "=" + escape(value);
        }

        highlight_row();
        function highlight_row(){
            var table = document.getElementById("loan");
            var cells = table.getElementsByTagName("td");
            for (var i = 0; i < cells.length; i++){
                //take each cell
                var cell = cells[i];
                //do something on onclick event for cell
                cell.onclick = function(){
                    //get the row id where the cell exists
                    var rowId = this.innerText;
                    createCookie("lid", rowId);
                    
                }
            }
        }
    
    </script>
</html>

<?php

    }else{

        echo'<script type="text/javascript">';
        echo'   alert("Select a Client");';
        echo'   window.location = "http://localhost/loanstar/allclient.php";';
        echo'</script>';
    }

?>