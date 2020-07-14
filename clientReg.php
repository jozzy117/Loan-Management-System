
<?php
    require 'config.php';

    $lastName = $_POST['lname'];
    $middleName = $_POST['mname'];
    $firstName = $_POST['fname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $acctNumber = $_POST['acctNo'];
    $bvn = $_POST['bvn'];
    $bankName = $_POST['bankName'];

    /*****
     uploading images from form
    ****/

    //$statusMsg = "";

    //file upload path
    $targetDir = "uploads/c/";
    $file_id = basename($_FILES["file"]["name"]);
    $file_passport = basename($_FILES["filePassport"]["name"]);
    $target_id_FilePath = $targetDir.$file_id;
    $target_passport_filePath = $targetDir.$file_passport;
    $id_fileType = pathinfo($target_id_FilePath,PATHINFO_EXTENSION);
    $passport_fileType = pathinfo($target_passport_filePath, PATHINFO_EXTENSION);

    if(isset($_POST["submit"]) && !empty($_FILES["file"]["name"]) && !empty($_FILES["filePassport"]["name"])){
        // Allow certain file formats
        $allowTypes = array('jpg','png','jpeg','gif','pdf');
        if (in_array($id_fileType, $allowTypes)){
            if (in_array($passport_fileType, $allowTypes)){
                //Upload to server
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_id_FilePath)){
                    if(move_uploaded_file($_FILES["filePassport"]["tmp_name"], $target_passport_filePath)){
                        // insert data into database
                        $sql = $conn->query("INSERT INTO clients (last_name,middle_name,first_name,client_address,phone,account_number,bvn,bank_name,id_card,passport) VALUES('$lastName','$middleName','$firstName','$address','$phone','$acctNumber','$bvn','$bankName','".$file_id."','".$file_passport."')");
                        if($sql){
                            echo'<script type="text/javascript">';
                            echo'   alert("Registration Successful");';
                            echo'   window.location = "http://localhost/loanstar/allclient.php";';
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

    //Display status message
    //echo $statusMsg;





?>