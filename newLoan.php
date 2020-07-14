<?php

session_start();
include 'headers/header.php';
require 'config.php';
include 'functions.php';

$clientID = $_SESSION['clientID'];
$loanID = generateLoanid();

?>

<html>
    <head>
        <link href="assets/css/table.css" rel="stylesheet">
    </head>
    <body>

        <div class="container">
                    <form name="newLoan" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data">
                        <h1>Loan Registration</h1><br>
                        <div class="row" id="info">
                            <div class="col-md-5 basic">
                                    <label for="lname" id="lbllast_name">Loan Amount (NGN)</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="number" name="loanAmount" id="loanAmount" required/><br><br>
                            </div>
                        </div>
                        <div class="row" id="ginfo">
                            <div class="col-md-5 guarantor">
                                    <h4>Guarantor Info</h4>
                                    <label for="phone" id="lblphone">Phone Number</label>
                                    <input class="box" type="number" name="gphone" id="gphone" required/><br><br>
                                    <label for="lname" id="lbllast_name">Last Name</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="glname" id="glname"  required/><br><br>
                                    <label for="mname" id="lblmiddle_name">Middle Name</label>
                                    &nbsp;&nbsp;&nbsp;<input class="box" type="text" name="gmname" id="gmname"  required/><br><br>
                                    <label for="fname" id="lblfirst_name">First Name</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="gfname" id="gfname"  required/><br><br>
                                    <label for="address" id="lbladdress">Address</label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="box" type="text" name="gaddress" id="gaddress"  required/><br><br>
                            </div>

                            <div class="col-md-2 loanfile">
                                <label for="file"> Upload ID (.jpg)</label>
                                <input type="file" id="file" name="file" required>
                                <label for="file" id="lblfile"> Upload Passport (.jpg)</label>
                                <input type="file" id="filePassport" name="filePassport"required>
                                <br><br><br>
                                <input type="button" id="cancel" name="cancel" style="margin:5px;" value="Cancel" onclick="history.go(-1)"/>
                                <input type="submit" id="submit" name="submit" style="margin:5px;" value="Submit"/><br>
                            </div>
                        </div>
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
    $loanAmount = $_POST['loanAmount'];
    $dailyPmt = dailyPayment($loanAmount);
    $createdtime = date('Y-m-d');
    $duedate = duedate($createdtime);
    $gphone = $_POST['gphone'];
    $glname = $_POST['glname'];
    $gmname = $_POST['gmname'];
    $gfname = $_POST['gfname'];
    $gaddress = $_POST['gaddress'];
    $statusMsg = "";
    $targetDir = "uploads/g/";
    $file_id = basename($_FILES["file"]["name"]);
    $file_passport = basename($_FILES["filePassport"]["name"]);
    $target_id_FilePath = $targetDir.$file_id;
    $target_passport_filePath = $targetDir.$file_passport;
    $id_fileType = pathinfo($target_id_FilePath,PATHINFO_EXTENSION);
    $passport_fileType = pathinfo($target_passport_filePath, PATHINFO_EXTENSION);

    if(!empty($_FILES["file"]["name"]) && !empty($_FILES["filePassport"]["name"])){
        // Allow certain file formats
        $allowTypes = array('jpg','png','jpeg','gif','pdf');
        if (in_array($id_fileType, $allowTypes)){
            if (in_array($passport_fileType, $allowTypes)){
                //Upload to server
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_id_FilePath)){
                    if(move_uploaded_file($_FILES["filePassport"]["tmp_name"], $target_passport_filePath)){
                        // insert data into database
                        $sql = $conn->query("INSERT INTO loans (loanid,clientid,loan_amount,exptDaily,glname,gmname,gfname,gnumber,gaddress,gidcard,gpassport,createdtime,dueDate) VALUES('$loanID','$clientID','$loanAmount','$dailyPmt','$glname','$gmname','$gfname','$gphone','$gaddress','".$file_id."','".$file_passport."','$createdtime','$duedate')");
                        if($sql){
                                    $start = strtotime($createdtime);//convert to unix timestamp
                                    $start += 86400;
                                    $enddate = strtotime($duedate);
                                    for($i=$start,$j=0; $i<=$enddate; $i+=86400,$j++){
                                                $today = date('w', ($i));
                                                    if($today == 0 || $today == 6){
                                                        continue;
                                                    }
                                                $days[$j] = date("Y-m-d", $i);
                                                $overdue = 0 - $dailyPmt;
                                                $res = $conn->query("INSERT INTO payments (loanid,exptpaydate,exptdailypmt,overdue) VALUES ('$loanID','$days[$j]','$dailyPmt','$overdue')");
                                    }
                            echo'<script type="text/javascript">';
                            echo'   alert("Loan Successful");';
                            echo'   window.location = "http://localhost/loanstar/clientDetails.php";';
                            echo'</script>';
                        //$statusMsg = "The file ".$file_id.",".$file_passport."has been uploaded successfully.";
                        }else{
                            echo'<script type="text/javascript">';
                            echo'   alert("File upload failed. please try again.");';
                            echo'</script>';
                        //$statusMsg = "File upload failed. please try again.";
                        }
                    }else{
                        echo'<script type="text/javascript">';
                        echo'   alert("There was an error uploading your file");';
                        echo'</script>';
                        //$statusMsg = "There was an error uploading your file";
                    }
                }else{
                    echo'<script type="text/javascript">';
                    echo'   alert("There was an error uploading your file");';
                    echo'</script>';
                    //$statusMsg = "There was an error uploading your file";
                }
            }else{
                echo'<script type="text/javascript">';
                echo'   alert("Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.");';
                echo'</script>';
                //$statusMsg = "Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.";
            }
        }else{
            echo'<script type="text/javascript">';
            echo'   alert("Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.");';
            echo'</script>';
            //$statusMsg = "Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.";
        }
    }else{
        echo'<script type="text/javascript">';
        echo'   alert("Please select a file to upload.");';
        echo'</script>';
        //$statusMsg = "Please select a file to upload.";
    }

    
}

?>