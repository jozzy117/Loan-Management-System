<?php
include 'headers/header.php';
require 'config.php';

$sql = $conn->query("SELECT id,last_name,middle_name,first_name,client_address,phone FROM clients");


?>

<div class ="container">
          <div class="row-fluid">
            <div class ="col-xs-6">
                <div class="row">
                    <div class="column">
                        <input type="text" id="myInput" onkeyup="search()" placeholder="search for names..">
                    </div>
                    <div class="">
                    <a href="clientReg.html"><input type="submit" id="newClient" name="newClient" value="New Client"/></a><br>
                    </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover" id="Client">
                    <tr>
                      <th>ID</th>
                      <th>NAME</th>
                      <th>ADDRESS</th>
                      <th>PHONE</th>
                    </tr>
                    <?php   if($sql->num_rows > 0){
                        while($row = $sql->fetch_assoc()){
                        echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . "<a href='clientDetails.php'>".$row["last_name"] . " ".$row["middle_name"]." " . $row["first_name"]. "</a>"."</td>";
                            echo "<td>" . $row["client_address"] . "</td>";
                            echo "<td>" . $row["phone"] . "</td>";
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
    function createCookie(name,value){
    document.cookie = escape(name) + "=" + escape(value);
    }

    highlight_row();
    function highlight_row(){
        var table = document.getElementById("Client");
        var cells = table.getElementsByTagName("td");
        for (var i = 0; i < cells.length; i++){
            //take each cell
            var cell = cells[i];
            //do something on onclick event for cell
            cell.onclick = function(){
                //get the row id where the cell exists
                var rowId = this.parentNode.rowIndex;
                createCookie("cliid", rowId);
            }
        }
    }

    function search(){
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("Client");
    tr = table.getElementsByTagName("tr");
        for (i = 0; i<tr.length; i++){
            td = tr[i].getElementsByTagName("td")[1];
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