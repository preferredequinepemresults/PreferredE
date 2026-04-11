<?php 

include_once("config.php");

$year_param =$_GET['year'];
$type_param =$_GET['type'];
$salecode_param =$_GET['salecode'];

setlocale(LC_MONETARY,"en_US");

$resultFound = fetchSalesAuctionReport($year_param,$type_param,$salecode_param);

$resultFound21 = fetchSalesSummary($year_param,$type_param,$salecode_param);


if($resultFound != ""){
    $delimiter = ",";
    $filename = "Auction-report_" . date('Y-m-d') . ".csv";
    ob_end_clean();
    // Create a file pointer
    $f = fopen('php://memory', 'w');
    
    // Set column headers
    $fields = array("No","SALECODE","TOTAL","GROSS","AVERAGE","$100,000 & OVER","$50,000 - $99,999","$25,000 - $49,999","$10,001 - $24,999",
        "$5,000 - $10,000","$4,999 & UNDER");
    
    fputcsv($f, $fields, $delimiter);
    
    // Output each row of the data, format line as csv and write to file pointer
    $number =0;
    foreach ( $resultFound as $row ) {
        $number = $number+1;
        $lineData = array($number,$row['Salecode'], "$".number_format($row['Total']), "$".number_format($row['Gross']), "$".number_format($row['Avg']), $row['V1']."%", $row['V2']."%",
            $row['V3']."%", $row['V4']."%", $row['V5']."%", $row['V6']."%");
        
        fputcsv($f, $lineData, $delimiter);
    }
    
    $fields2 = array("No","SALECODE","TOP-PRICE PACER","TOP-PRICE TROTTER");
    
    fputcsv($f, array(""), $delimiter);
    fputcsv($f, array(""), $delimiter);
    fputcsv($f, $fields2, $delimiter);
    $number =0;
    foreach ( $resultFound21 as $row ) {
        $number = $number+1;
        $lineData = array($number,$row['Salecode'], "$".number_format($row['PMax'])." - ".$row['Pacer'], "$".number_format($row['TMax'])." - ".$row['Trotter']);
        
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