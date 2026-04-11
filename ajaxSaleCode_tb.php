<option value="">Sale Code Filter</option>
<option value="">All Salecode</option>
<?php
include_once("config.php");

$year_param =$_GET['year'];
$resultList = fetchSalecodeList_tb($year_param);

foreach($resultList as $row) {
    echo '<option>'.$row['Salecode'].'</option>';
}
?>