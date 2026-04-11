<?php
include("./header.php");
echo '<br>';
echo '<br>';
?>
<head>
  <meta name="viewport" content="width=device-width initial-scale=1">
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
$rowCount=0;
$rowCountSales=0;
$response="";
$message="";
if (isset($_POST["import"])) {
    
    $fileName = $_FILES["file"]["tmp_name"];
    
    if ($_FILES["file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            
            
            $rowCount = $rowCount + 1;
            if($rowCount == 1)
            {
                continue;
            }
            
            $hip = "";
            if (isset($column[0])) {
                $hip = mysqli_real_escape_string($conn, $column[0]);
            }
            $salecode = "";
            if (isset($column[1])) {
                $salecode = mysqli_real_escape_string($conn, $column[1]);
            }
            $datefoal = "0000-00-00";
            if ($column[2] != "" and isset($column[2])) {
                $datefoal = mysqli_real_escape_string($conn, $column[2]);
                $date=date_create($datefoal);
                $datefoal = date_format($date,"Y-m-d");
            }
            $rating = "";
            if (isset($column[3])) {
                $rating = mysqli_real_escape_string($conn, $column[3]);
            }
            
            $saleID =checkSalesforUpdate($hip,$salecode,$datefoal);
            
            if ($saleID != "") {
                $sqlInsert = "UPDATE sales SET
                RATING = ?
                WHERE SALEID =".$saleID;
                
                $paramType = "s";
                
                $update_data_stmt = mysqli_stmt_init($conn);
                
                if (!mysqli_stmt_prepare($update_data_stmt, $sqlInsert)){
                    $response = "Error";
                    $message = "Problem in Importing CSV Data: Data is not in proper format".mysqli_error($conn);
                } else {
                    mysqli_stmt_bind_param($update_data_stmt, $paramType, 
                        $rating);
                    mysqli_stmt_execute($update_data_stmt);
                    $response = "Success";
                    $message = "CSV Data Updated into the Database";
                }
                
                $rowCountSales = $rowCountSales + 1;
            }
        }
        echo "<h1 style='text-align:center;color:#D98880;'>".$response." :- ".$message."</h3>";
        echo '<br>';
        if ($saleID != "") {
            echo "<h3 style='text-align:center;color:#D98880;'>"."Sales Rows Updated:- ".$rowCountSales."</h2>";
        }
        
    }
}

?>