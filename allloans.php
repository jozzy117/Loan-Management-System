<?php

include 'headers/loanheader.php';
require 'config.php';

$paid = 0;
$overdue = 0;
$todayPay = 0;



?>
                <div class ="container">
                    <div class="column">
                        <input type="text" id="myInput" onkeyup="search()" placeholder="search for overdue..">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="loan">
                            <tr>
                                <th>CID</th>
                                <th>LOAN-ID</th>
                                <th>AMOUNT</th>
                                <th>PAID</th>
                                <th>BALANCE</th>
                                <th>OVER-DUE</th>
                            </tr>
                                <?php
                                $sql = $conn->query("SELECT clientid,loanid,loan_amount FROM loans ORDER BY clientid ASC");
                                //if($sql->num_rows > 0){
                                    while($row = $sql->fetch_assoc()){
                                    echo "<tr>";
                                        echo "<td>" . "<a href='clientDetails.php'>". $row["clientid"] . "</a>"."</td>";
                                        echo "<td>" . $row["loanid"] ."</td>";
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
            
                               // }
                                $sql->close();
                                ?>
                        </table>
                    </div>
                </div>

<script>

    function search(){
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("loan");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i<tr.length; i++){
            td = tr[i].getElementsByTagName("td")[5];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter)>-1){
                    tr[i].style.display = "";
                }else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

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
                createCookie("cliid", rowId);
                    
            }
        }
    }
  
</script>