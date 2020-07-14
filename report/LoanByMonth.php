<?php
//require_once __DIR__."/koolreport/core/autoload.php";
require_once dirname(__FILE__)."/../koolreport/core/autoload.php";
use \koolreport\processes\Group;
use \koolreport\processes\Sort;
use \koolreport\processes\Limit;

class LoanByMonth extends \koolreport\KoolReport
{
    public function settings()
    {
        return array(
            "dataSources"=>array(
                "loanstar"=>array(
                    "connectionString"=>"mysql:host=localhost;dbname=loanstar",
                    "username"=>"root",
                    "password"=>"",
                    "charset"=>"utf8"
                )
            )
        );
    }

    public function setup()
    {
        $this->src('loanstar')
        ->query("SELECT month(createdtime) as month_num,MONTHNAME(createdtime) as month,loan_amount FROM loans")
        ->pipe(new Group(array(
            "by"=>"month",
            "sum"=>"loan_amount"
        )))
        ->pipe(new Sort(array(
            "month_num"=>"asc"
        )))
        ->pipe(new Limit(array(10)))
        ->pipe($this->dataStore('loan_by_month'));
    }
}