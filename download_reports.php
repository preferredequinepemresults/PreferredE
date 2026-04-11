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

$resultFound = fetchIndividualSaleData($year_param,$salecode_param,$type_param,$elig_param,$gait_param,
    $sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample.csv"');
$data = array(
    'aaa,bbb,ccc,dddd',
    '123,456,789',
    '"aaa","bbb"'
);

$fp = fopen('php://output', 'wb');
$data = "";
foreach ( $resultFound as $line ) {
    $data = "";
    foreach ( $line as $elements ) {
    //print_r($line);
    $val = explode(",", $elements);
    $data = $val.",";
    }
    //$valLine = explode(",", $data);
    fputcsv($fp, $data); 
}
fclose($fp);

?>