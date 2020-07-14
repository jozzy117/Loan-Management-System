<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\ColumnChart;
?>

<!-- <div class="text-center">
    <h1>Disbursement Report</h1>
    <h4>This report shows Monthly Disbursement for the year</h4>
</div>
<hr/> -->

<?php
    ColumnChart::create(array(
        "dataStore"=>$this->dataStore('loan_by_month'),
        "width"=>"70%",
        "height"=>"500px",
        "columns"=>array(
            "month"=>array(
                "label"=>"Month"
            ),
            "loan_amount"=>array(
                "type"=>"number",
                "label"=>"Amount",
                "prefix"=>"NGN",
            )
        ),
        "options"=>array(
            "title"=>"Annual Disbursement"
        ),
        "colorScheme"=>array(
            "#9B2743"
        )
    ));
?>
