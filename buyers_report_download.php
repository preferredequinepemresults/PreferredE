<?php 

include_once("config.php");

$salecode_param =$_GET['salecode'];
$year_param =$_GET['year'];
$type_param =$_GET['type'];
$sort1_param =$_GET['sort1'];
$sort2_param =$_GET['sort2'];
$sort3_param =$_GET['sort3'];
$sort4_param =$_GET['sort4'];
$sort5_param =$_GET['sort5'];

setlocale(LC_MONETARY,"en_US");

$resultFound = fetchBuyersReport($salecode_param,$year_param,$type_param,$sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);

$number =0;
if($resultFound != ""){
    $delimiter = ",";
    $filename = "Buyers-report_" . date('Y-m-d') . ".csv";
    ob_end_clean();
    // Create a file pointer
    $f = fopen('php://memory', 'w');
    
    // Set column headers
    $fields = array("No","HIP","Horse","Type","Price","Currency","Salecode","Day","Buyer Last Name","Buyer First Name","SB City","SB State","SB Country");
    
    fputcsv($f, $fields, $delimiter);
    
    // Output each row of the data, format line as csv and write to file pointer
    foreach ( $resultFound as $row ) {
        $number = $number+1;
        $lineData = array($number,$row['HIP'], $row['Horse'], $row['Type'], "$".number_format($row['Price']), $row['Currency'], $row['Salecode'],
            $row['Day'], $row['Purlname'], $row['Purfname'], $row['Sbcity'], $row['Sbstate'], $row['Sbcountry']);
        
        fputcsv($f, $lineData, $delimiter);
    }
    
    // Move back to beginning of file
    fseek($f, 0);
    
    // Set headers to download file rather than displayed
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    
    //output all remaining data on a file pointer
    fpassthru($f);
}
exit;

?>