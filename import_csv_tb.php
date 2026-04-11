<?php
include("./header.php");
echo '<br>';
echo '<br>';
?>

<head>
    <meta name="viewport" content="width=device-width</h4> initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/table.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>


</head>
<?php
include_once("config.php");

use Phppot\DataSource;

require_once 'DataSource.php';
$db = new DataSource();
$conn = $db->getConnection();
$rowCount = 0;
$rowCountSales = 0;
$rowCountDamsire = 0;
$response = "";
$message = "";
if (isset($_POST["import"])) {

    $fileName = $_FILES["file"]["tmp_name"];

    $targetDir = 'uploads/';
    $targetFilePath = $targetDir . basename($_FILES["file"]["name"]);

    // Assuming $mysqli is your MySQL connection
    global $mysqli;

    $originalFileName = basename($_FILES["file"]["name"]);
    $fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);

    // Check if the file already exists in the database
    $checkExistingFileSql = "SELECT file_name FROM documents WHERE file_name = '$fileNameWithoutExtension'";
    $resultExistingFile = mysqli_query($mysqli, $checkExistingFileSql);

    if (mysqli_num_rows($resultExistingFile) > 0) {
        // File already exists, delete previous records and the file from the codebase
        $deletePreviousRecordsSql = "DELETE FROM documents WHERE file_name = '$fileNameWithoutExtension'";
        mysqli_query($mysqli, $deletePreviousRecordsSql);

        // Delete the file from the codebase
        unlink($targetFilePath);
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // Insert file details into the database with the current timestamp
        $sql = "INSERT INTO documents (file_name, upload_date) VALUES ('$fileNameWithoutExtension', NOW())";

        $result = mysqli_query($mysqli, $sql);
    }

    if ($_FILES["file"]["size"] > 0) {

        $file = fopen($targetFilePath, "r");

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {


            $rowCount = $rowCount + 1;
            if ($rowCount == 1) {
                continue;
            }

            $sire = "";
            if (isset($column[21])) {
                $sire = mysqli_real_escape_string($conn, $column[21]);
                $sire = str_replace("\\", "", $sire);
                //echo $sire."|";
            }
            $csire = "";
            if (isset($column[22])) {
                $csire = mysqli_real_escape_string($conn, $column[22]);
            }
            $dam = "";
            if (isset($column[23])) {
                $dam = mysqli_real_escape_string($conn, $column[23]);
                $dam = str_replace("\\", "", $dam);
            }
            $cdam = "";
            if (isset($column[24])) {
                $cdam = mysqli_real_escape_string($conn, $column[24]);
            }
            $sireofdam = "";
            if (isset($column[25])) {
                $sireofdam = mysqli_real_escape_string($conn, $column[25]);
                $sireofdam = str_replace("\\", "", $sireofdam);
            }
            $csireofdam = "";
            if (isset($column[26])) {
                $csireofdam = mysqli_real_escape_string($conn, $column[26]);
            }
            $damofdam = "";
            if (isset($column[27])) {
                $damofdam = mysqli_real_escape_string($conn, $column[27]);
                $damofdam = str_replace("\\", "", $damofdam);
            }
            $cdamofdam = "";
            if (isset($column[28])) {
                $cdamofdam = mysqli_real_escape_string($conn, $column[28]);
            }
            $damtatt = "";
            if (isset($column[29])) {
                $damtatt = mysqli_real_escape_string($conn, $column[29]);
            }
            $damyof = 0;
            if (isset($column[30])) {
                $damyof = mysqli_real_escape_string($conn, $column[30]);
            }
            $ddamtatt = "";
            if (isset($column[31])) {
                $ddamtatt = mysqli_real_escape_string($conn, $column[31]);
            }

            $damsire_ID = getTDamsireID($csire, $cdam);
            //             echo 'exist--'.$damsire_ID;
            //             echo '<br>';


            //             echo $sire."--".
            //                 $csire."--".
            //                 $dam."--".
            //                 $cdam."--".
            //                 $sireofdam."--".
            //                 $csireofdam."--".
            //                 $damofdam."--".
            //                 $cdamofdam."--".
            //                 $damtatt."--".
            //                 $ddamtatt;
            if ($damsire_ID == "") {
                $sqlInsertDamsire = "INSERT into tdamsire
                    (SIRE,CSIRE,DAM,CDAM,SIREOFDAM,CSIREOFDAM,DAMOFDAM,CDAMOFDAM,DAMTATT,DAMYOF,DDAMTATT)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                $paramType = "sssssssssis";

                $paramArray1 = array(
                    $sire,
                    $csire,
                    $dam,
                    $cdam,
                    $sireofdam,
                    $csireofdam,
                    $damofdam,
                    $cdamofdam,
                    $damtatt,
                    $damyof,
                    $ddamtatt
                );
                $insertId = $db->insert($sqlInsertDamsire, $paramType, $paramArray1);

                if (! empty($insertId)) {
                    $response = "Success";
                    $message = "CSV Data Imported into the Database";
                } else {
                    $response = "Error";
                    $message = "Problem in Importing CSV Data: Data in not in proper format";
                }
                $rowCountDamsire = $rowCountDamsire + 1;
                $damsire_ID = getLastTDamsireID();
                //echo "|newDamsire_ID--".$damsire_ID;
            }

            //--------

            $saleyear = 0;
            if (isset($column[0])) {
                $saleyear = mysqli_real_escape_string($conn, $column[0]);
            }
            $currency = "";
            if (isset($column[43])) {
                $currency = mysqli_real_escape_string($conn, $column[43]);
            }
            $saletype = "";
            if (isset($column[1])) {
                $saletype = mysqli_real_escape_string($conn, $column[1]);
            }
            $salecode = "";
            if (isset($column[2])) {
                $salecode = mysqli_real_escape_string($conn, $column[2]);
            }
            $book = "";
            if (isset($column[4])) {
                $book = mysqli_real_escape_string($conn, $column[4]);
            }
            $tattoo = "";
            if (isset($column[11])) {
                $tattoo = mysqli_real_escape_string($conn, $column[11]);
            }
            $hip = "";
            if (isset($column[6])) {
                $hip = mysqli_real_escape_string($conn, $column[6]);
            }
            $horse = "";
            if (isset($column[8])) {
                $horse = mysqli_real_escape_string($conn, $column[8]);
                $horse = str_replace("\\", "", $horse);
            }
            $chorse = "";
            if (isset($column[9])) {
                $chorse = mysqli_real_escape_string($conn, $column[9]);
            }
            $sex = "";
            if (isset($column[15])) {
                $sex = mysqli_real_escape_string($conn, $column[15]);
            }
            $type = "";
            if (isset($column[17])) {
                $type = mysqli_real_escape_string($conn, $column[17]);
            }
            $color = "";
            if (isset($column[14])) {
                $color = mysqli_real_escape_string($conn, $column[14]);
            }
            $gait = "";
            if (isset($column[16])) {
                $gait = mysqli_real_escape_string($conn, $column[16]);
            }
            $price = 0;
            if ($column[42] != "" and isset($column[42])) {
                $price = mysqli_real_escape_string($conn, $column[42]);
            }

            $saledate = "0000-00-00";
            if ($column[3] != "" && isset($column[3])) {
                $saledate = mysqli_real_escape_string($conn, $column[3]);
                $date = date_create($saledate);
                if ($date !== false) {
                    $saledate = date_format($date, "Y-m-d");
                } else {
                    $saledate = "1901-01-01"; // Set default value for invalid date
                }
            }

            $record = "";
            if (isset($column[18])) {
                $record = mysqli_real_escape_string($conn, $column[18]);
            }

            $datefoal = "0000-00-00";
            if ($column[12] != "" && isset($column[12])) {
                $datefoal = mysqli_real_escape_string($conn, $column[12]);
                $date = date_create($datefoal);
                if ($date !== false) {
                    $datefoal = date_format($date, "Y-m-d");
                } else {
                    $datefoal = "1901-01-01"; // Set default value for invalid date
                }
            }

            $bredto = "";
            if (isset($column[32])) {
                $bredto = mysqli_real_escape_string($conn, $column[32]);
            }

            $lastbred = "0000-00-00";
            if (!empty($column[33]) && isset($column[33])) {
                $lastbred = mysqli_real_escape_string($conn, $column[33]);
                $date = date_create($lastbred);
                if ($date !== false) {
                    $lastbred = date_format($date, "Y-m-d");
                } else {
                    $lastbred = "1901-01-01"; // Set default value for invalid date
                }
            }

            $sbcity = "";
            if (isset($column[39])) {
                $sbcity = mysqli_real_escape_string($conn, $column[39]);
            }
            $sbstate = "";
            if (isset($column[40])) {
                $sbstate = mysqli_real_escape_string($conn, $column[40]);
            }
            $sbcountry = "";
            if (isset($column[41])) {
                $sbcountry = mysqli_real_escape_string($conn, $column[41]);
            }
            $purfname = "";
            if (isset($column[37])) {
                $purfname = mysqli_real_escape_string($conn, $column[37]);
                $purfname = str_replace("\\", "", $purfname);
            }
            $purlname = "";
            if (isset($column[38])) {
                $purlname = mysqli_real_escape_string($conn, $column[38]);
                $purlname = str_replace("\\", "", $purlname);
            }
            $conslname = "";
            if (isset($column[34])) {
                $conslname = mysqli_real_escape_string($conn, $column[34]);
                $conslname = str_replace("\\", "", $conslname);
            }
            $consno = "";
            if (isset($column[35])) {
                $consno = mysqli_real_escape_string($conn, $column[35]);
            }
            $pemcode = "";
            if (isset($column[36])) {
                $pemcode = mysqli_real_escape_string($conn, $column[36]);
            }
            $age = 0;
            if ($column[13] != "" and isset($column[13])) {
                $age = mysqli_real_escape_string($conn, $column[13]);
            }
            $et = "";
            if (isset($column[19])) {
                $et = mysqli_real_escape_string($conn, $column[19]);
            }
            $hipnum = 0;
            if ($column[7] != "" and isset($column[7])) {
                $hipnum = mysqli_real_escape_string($conn, $column[7]);
            }
            $day = 0;
            if (isset($column[5]) && $column[5] !== "") {
                // Direct conversion to int (PHP handles string to int conversion)
                $day = (int)$column[5];
                
                // Safety check
                if ($day < 1) {
                    $day = 1; // Minimum day
                }
            }
            $elig = "";
            if (isset($column[20])) {
                $elig = mysqli_real_escape_string($conn, $column[20]);
            }
            $rating = "";
            if (isset($column[10])) {
                $rating = mysqli_real_escape_string($conn, $column[10]);
            }
            $url = "";
            if (isset($column[44])) {
                $url = mysqli_real_escape_string($conn, $column[44]);
            }
            $NFFM = "";
            if (isset($column[45])) {
                $NFFM = mysqli_real_escape_string($conn, $column[45]);
            }
            $privatesale = "";
            if (isset($column[46])) {
                $privatesale = mysqli_real_escape_string($conn, $column[46]);
            }
            $breed = "";
            if (isset($column[47])) {
                $breed = mysqli_real_escape_string($conn, $column[47]);
            }
            $yearFoal = 0;
            if (isset($column[48])) {
                $yearFoal = mysqli_real_escape_string($conn, $column[48]);
            }

            $tSire = "";
            if (isset($column[49])) {
                $tSire = mysqli_real_escape_string($conn, $column[49]);
            }

            $tSireofdam = "";
            if (isset($column[50])) {
                $tSireofdam = mysqli_real_escape_string($conn, $column[50]);
            }

            $TDAM = "";
            if (isset($column[51])) {
                $TDAM = mysqli_real_escape_string($conn, $column[51]);
            }

            $UTT = 0.0;
            if (isset($column[52])) {
                $UTT = mysqli_real_escape_string($conn, $column[52]);
            }

            $STATUS = "";
            if (isset($column[53])) {
                $STATUS = mysqli_real_escape_string($conn, $column[53]);
            }

            $farmname = "";
            if (isset($column[54])) { // Adjust the column index based on your CSV structure
                $farmname = mysqli_real_escape_string($conn, $column[54]);
                $farmname = str_replace("\\", "", $farmname);
            }

            $farmcode = "";
            if (isset($column[55])) { // Adjust the column index based on your CSV structure
                $farmcode = mysqli_real_escape_string($conn, $column[55]);
            }

            $salebarn = "";
            if (isset($column[56])) {
                $salebarn = mysqli_real_escape_string($conn, $column[56]);
            }

            $salesection = "";
            if (isset($column[57])) {
                $salesection = mysqli_real_escape_string($conn, $column[57]);
            }

            $salestall = "";
            if (isset($column[58])) {
                $salestall = mysqli_real_escape_string($conn, $column[58]);
            }



            //                 echo '<br>';
            //                 echo $tattoo.'|'.
            //                     $hip.'|'.
            //                     $horse.'|'.
            //                     $chorse.'|'.
            //                     $sex.'|'.
            //                     $type.'|'.
            //                     $color.'|'.
            //                     $gait.'|'.
            //                     $price.'|'.
            //                     $salecode.'|'.
            //                     $saledate.'|'.
            //                     $record.'|'.
            //                     $datefoal.'|'.
            //                     $bredto.'|'.
            //                     $lastbred.'|'.
            //                     $sbcity.'|'.
            //                     $sbstate.'|'.
            //                     $sbcountry.'|'.
            //                     $purfname.'|'.
            //                     $purlname.'|'.
            //                     $conslname.'|'.
            //                     $consno.'|'.
            //                     $pemcode.'|'.
            //                     $age.'|'.
            //                     $saletype.'|'.
            //                     $et.'|'.
            //                     $hipnum.'|'.
            //                     $day.'|'.
            //                     $elig.'|'.
            //                     $rating.'|'.
            //                     $url.'|'.
            //                     $damsire_ID;
            //                     echo '<br>';
            $saleID = checkTSalesData($tattoo, $hip, $chorse, $salecode, $saledate);


            if ($saleID == "") {
                $sqlInsert = "INSERT into tsales
                (TATTOO,BREED,HIP,HORSE,CHORSE,SEX,`TYPE`,COLOR,GAIT,PRICE,SALECODE,SALEDATE,RECORD,DATEFOAL,
                BREDTO,LASTBRED,SBCITY,SBSTATE,SBCOUNTRY,PURFNAME,PURLNAME,CONSLNAME,CONSNO,PEMCODE,
                AGE,SALETYPE,ET,HIPNUM,`DAY`,ELIG,RATING,`URL`,PRIVATESALE,DAMSIRE_ID,SALEYEAR,BOOK,CURRENCY,NFFM,YEARFOAL,tSire,tSireofdam,TDAM,UTT,`STATUS`,FARMNAME,FARMCODE,salebarn,salesection,salestall)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $paramType = "sssssssssdssssssssssssssissiissssiisssisssdssssss";
                $paramArray = array(
                    $tattoo,
                    $breed,
                    $hip,
                    $horse,
                    $chorse,
                    $sex,
                    $type,
                    $color,
                    $gait,
                    $price,
                    $salecode,
                    $saledate,
                    $record,
                    $datefoal,
                    $bredto,
                    $lastbred,
                    $sbcity,
                    $sbstate,
                    $sbcountry,
                    $purfname,
                    $purlname,
                    $conslname,
                    $consno,
                    $pemcode,
                    $age,
                    $saletype,
                    $et,
                    $hipnum,
                    $day,
                    $elig,
                    $rating,
                    $url,
                    $privatesale,
                    $damsire_ID,
                    $saleyear,
                    $book,
                    $currency,
                    $NFFM,
                    $yearFoal,
                    $tSire,
                    $tSireofdam,
                    $TDAM,
                    $UTT,
                    $STATUS,
                    $farmname,
                    $farmcode,
                    $salebarn,
                    $salesection,
                    $salestall
                );

                $insertId = $db->insert($sqlInsert, $paramType, $paramArray);

                if (! empty($insertId)) {
                    $response = "Success";
                    $message = "CSV Data Imported into the Database";
                } else {
                    $response = "Error";
                    $message = "Problem in Importing CSV Data: Data in not in proper format";
                }
                $rowCountSales = $rowCountSales + 1;
            } else {
                //                 $response = "Success";
                //                 $message = "Data Already Exist";
                $sqlInsert = "UPDATE tsales SET
                TATTOO = ?,BREED = ?, HIP = ?,HORSE = ?,CHORSE = ?,SEX = ?,`TYPE` = ?,COLOR = ?,GAIT = ?,PRICE = ?,
                SALECODE = ?,SALEDATE = ?,RECORD = ?,DATEFOAL = ?,BREDTO = ?,LASTBRED = ?,SBCITY = ?,SBSTATE = ?,
                SBCOUNTRY = ?,PURFNAME = ?,PURLNAME = ?,CONSLNAME = ?,CONSNO = ?,PEMCODE = ?,
                AGE = ?,SALETYPE = ?,ET = ?,HIPNUM = ?,`DAY` = ?,ELIG = ?,RATING = ?,`URL` = ?,PRIVATESALE = ?,
                DAMSIRE_ID = ?,SALEYEAR = ?,BOOK = ?,CURRENCY = ?,NFFM = ?, YEARFOAL = ?, tSire = ?, tSireofdam = ?, TDAM = ?, UTT = ?, `STATUS` = ?, FARMNAME = ?, FARMCODE = ?, salebarn = ?, salesection = ?, salestall = ?
                WHERE SALEID =" . $saleID;

                $paramType = "sssssssssdssssssssssssssissiissssiisssisssdssssss";
                //echo $sqlInsert;

                $update_data_stmt = mysqli_stmt_init($conn);

                if (!mysqli_stmt_prepare($update_data_stmt, $sqlInsert)) {
                    $response = "Error";
                    $message = "Problem in Importing CSV Data: Data is not in proper format" . mysqli_error($conn);
                } else {
                    mysqli_stmt_bind_param(
                        $update_data_stmt,
                        $paramType,
                        $tattoo,
                        $breed,
                        $hip,
                        $horse,
                        $chorse,
                        $sex,
                        $type,
                        $color,
                        $gait,
                        $price,
                        $salecode,
                        $saledate,
                        $record,
                        $datefoal,
                        $bredto,
                        $lastbred,
                        $sbcity,
                        $sbstate,
                        $sbcountry,
                        $purfname,
                        $purlname,
                        $conslname,
                        $consno,
                        $pemcode,
                        $age,
                        $saletype,
                        $et,
                        $hipnum,
                        $day,
                        $elig,
                        $rating,
                        $url,
                        $privatesale,
                        $damsire_ID,
                        $saleyear,
                        $book,
                        $currency,
                        $NFFM,
                        $yearFoal,
                        $tSire,
                        $tSireofdam,
                        $TDAM,
                        $UTT,
                        $STATUS,
                        $farmname,
                        $farmcode,
                        $salebarn,
                        $salesection,
                        $salestall
                    );
                    mysqli_stmt_execute($update_data_stmt);
                    $response = "Success";
                    $message = "CSV Data Updated into the Database";
                }

                $rowCountSales = $rowCountSales + 1;
            }
        }
        echo "<h1 style='text-align:center;color:#D98880;'>" . $response . " :- " . $message . "</h3>";
        echo '<br>';
        echo "<h3 style='text-align:center;color:#D98880;'>" . "Thorough Breed Damsire Rows Inserted:- " . $rowCountDamsire . "</h2>";
        echo '<br>';
        echo "<h3 style='text-align:center;color:#D98880;'>" . "Thorough Breed Sales Rows Inserted:- " . $rowCountSales . "</h2>";
    }
}

?>