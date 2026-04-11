<?php 

include_once("config.php");
//include_once("download_reports.php");
$year_param =$_GET['year'];
$salecode_param =$_GET['salecode'];
$type_param =$_GET['type'];
$elig_param =$_GET['elig'];
$gait_param =$_GET['gait'];
$sort1_param =$_GET['sort1'];
$sort2_param =$_GET['sort2'];
$sort3_param =$_GET['sort3'];
$sort4_param =$_GET['sort4'];
$sort5_param =$_GET['sort5'];
setlocale(LC_MONETARY,"en_US");

$resultFound = fetchIndividualSaleData($year_param,$salecode_param,$type_param,$elig_param,$gait_param,
    $sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);

$number =0;
if($resultFound != ""){
    $delimiter = ",";
    $filename = "Individual-sales-data_" . date('Y-m-d') . ".csv";
    ob_end_clean();
    // Create a file pointer
    $f = fopen('php://memory', 'w');
    
    // Set column headers
    $fields = array("No","Overall Rank","Fillies Rank","Colts Rank","HIP","Horse","Sex","Colour","Gait","Type","ET","DOB","Elig","Sire","Dam","SaleCode","Consno","Saledate","Day","Price","Curr","PLastName","PFirstName","Rate");
    
    fputcsv($f, $fields, $delimiter);
    
    // Output each row of the data, format line as csv and write to file pointer
    foreach ( $resultFound as $row ) {
        $number = $number+1;
        $lineData = array($number,$row['ORank'], $row['Frank'], $row['CRank'], $row['HIP'], $row['Horse'], $row['Sex'], $row['Color'], $row['Gait'], $row['Type'], $row['ET'], $row['Datefoal'], $row['Elig'], $row['Sire'], 
            $row['Dam'], $row['Salecode'], $row['Consno'], $row['Saledate'], $row['Day'], "$".number_format($row['Price']), $row['Currency'], $row['Purlname'], $row['Purfname'], $row['Rating']);
        
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