<?php 
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample.csv"');
$data = array(
    'aaa,bbb,ccc,dddd',
    '123,456,789',
    '"aaa","bbb"'
);

$fp = fopen('php://output', 'wb');
foreach ( $data as $line ) {
    $val = explode(",", $line);
    fputcsv($fp, $val);
}
fclose($fp);

?>