
<?php

include 'headers/indexheader.php';
require 'config.php';
include 'functions.php';

require_once "./report/LoanByMonth.php";

$loanByMonth = new LoanByMonth;

?>

<div class="container">
<div class="weekly-report">
<table class="table table-bordered">
			<thead>
			  <tr>
				<th>Today</th>
				<th>This Week</th>
				<th>This Month</th>
			  </tr>
			</thead>
			<tbody>
<?php

	$sql = mysqli_query($conn,"select sum(loan_amount) as total_amount from loans WHERE MONTH(createdtime) = MONTH(NOW())");
	$result = mysqli_query($conn,"select sum(loan_amount) as total_amount from loans WHERE YEARWEEK(createdtime) = YEARWEEK(NOW())");
	$db = mysqli_query($conn,"select sum(loan_amount) as total_amount from loans WHERE YEAR(createdtime) = YEAR(NOW()) AND MONTH(createdtime) = MONTH(NOW()) AND DAY(createdtime) = DAY(NOW())");
	//$i=0;
	while($row = $db->fetch_assoc()){ ?>
		<tr>
		<td><?php echo $row["total_amount"]; ?></td>
	<?php
	}
	while($row = $result->fetch_assoc()) {
	
?>
				<td><?php echo $row["total_amount"]; ?></td>
	<?php
	}
		while ($row = $sql->fetch_assoc()){ ?>
			<td><?php echo $row["total_amount"]; ?></td>
			</tr>
		<?php
		}
			  
			 
//$i++;
	
?>
			</tbody>
  </table>
</div>

<div class="chart">
	<?php
	
	$loanByMonth->run()->render();
	?>
</div>

<div >
        <input type="text" id="myInput" onkeyup="search()" placeholder="search for months..">
</div>
<div class ="monthly-report" id="report">
    <table class="table table-bordered">
			<thead>
			  <tr>
				<th>Year</th>
				<th>Month</th>
				<th>Disbursement</th>
			  </tr>
			</thead>
			<tbody>
<?php
	$result = mysqli_query($conn,"select year(createdtime) as year, month(createdtime) as month, sum(loan_amount) as total_amount
	from loans
	group by year(createdtime), month(createdtime)");
	$i=0;
	if($result->num_rows>0){
	while($row = $result->fetch_assoc()) {
	$monthNum  = $row["month"];
	$dateObj   = DateTime::createFromFormat('!m', $monthNum);
	$monthName = $dateObj->format('F');
?>
			  <tr>
				<td><?php echo $row["year"]; ?></td>
				<td><?php echo $monthName; ?></td>
				<td><?php echo $row["total_amount"]; ?></td>
			  </tr>
			  <?php
$i++;
}
	}
?>
			</tbody>
  </table>
</div>


</div>

<script>
    function search(){
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput");
		filter = input.value.toUpperCase();
		table = document.getElementById("report");
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
