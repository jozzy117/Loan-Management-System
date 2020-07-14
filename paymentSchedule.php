
<?php
include 'headers/loanheader.php';
require 'config.php';

$loanid = $_COOKIE["lid"];

$sql = $conn->query("SELECT payment_amount, createdtime from payments WHERE loanid = '$loanid'and payment_status = 'paid'");


?>

<div class ="container">
          <div class="row-fluid">
            <div class ="col-xs-6">
                <div class="row">
                    <div class="column">
                        <input type="text" id="myInput" onkeyup="search()" placeholder="search for dates..">
                    </div>
                    <div class="column">
                    <input type="button" id="back" name="back" value="Back" onclick="history.go(-1)"/><br>
                    </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover" id="Client">
                    <tr>
                      <th>DATE</th>
                      <th>AMOUNT</th>
                    </tr>
                    <?php   if($sql->num_rows > 0){
                        while($row = $sql->fetch_assoc()){
                        echo "<tr>";
                            echo "<td>" . $row["createdtime"] . "</td>";
                            echo "<td>" . $row["payment_amount"] . "</td>";
                            echo "</tr>";
                        }

                    }
                    $sql->close();
                    ?>
                  </table>
                </div>
            </div>
          </div>
        </div>

        <script>
          function search(){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("Client");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i<tr.length; i++){
        td = tr[i].getElementsByTagName("td")[0];
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
  
        </script>