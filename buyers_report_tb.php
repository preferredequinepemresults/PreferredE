<?php
include("./header.php");
include("./session_page.php");
?>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/css/table.css">
  
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
</head>
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

$resultFound = fetchBuyersReport_tb($salecode_param,$year_param,$type_param,$sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);

$yearList = getYearsList_tb();
$resultList = fetchSalecodeList_tb($year_param);
$typeList = fetchTypeList_tb();

$sortList = array("Hip","Horse","Type", "Sire", "Dam", "Datefoal", "Price Desc", "Salecode", "Purlname", "Purfname");
?>
<br>


<div style= "margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">THOROUGHBRED BUYER'S REPORT </h1>


<select class="custom-select1" id="year">
	<option value="">Sale Year</option>
	<option value="">All Years</option>
	<?php foreach($yearList as $row) {
	    echo '<option>'.$row['Year'].'</option>';
    } ?>
</select>
 <select class="custom-select1" id="salecode">
	<option value="">Sale Code Filter</option>
	<option value="">All Salecode</option>
	<?php foreach($resultList as $row) {
	    echo '<option>'.$row['Salecode'].'</option>';
    } ?>
</select>
 <select class="custom-select1" id="type">
	<option value="">Type Filter</option>
	<option value="">All Types</option>
<!-- 	<option value="B">B : Broodmare</option> -->
<!-- 	<option value="LB">LB : Lifetime Breeding</option> -->
<!-- 	<option value="M">M : Maiden Broodmare</option> -->
<!-- 	<option value="P">P : Broodmare Prospect</option> -->
<!-- 	<option value="R">R : Race Horse</option> -->
<!-- 	<option value="S">S : Share</option> -->
<!-- 	<option value="SEA">SEA : Season</option> -->
<!-- 	<option value="T">T : Stallion</option> -->
<!-- 	<option value="W">W : Weanling</option> -->
<!-- 	<option value="Y">Y : Yearling</option> -->
	<?php 
    foreach($typeList as $row) {
	    echo '<option>'.$row['Type'].'</option>';
    } 
    ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort1"> 
	<option  value="">Sort By 1st</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort2">
	<option value="">Sort By 2nd</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort3">
	<option value="">Sort By 3rd</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort4">
	<option value="">Sort By 4th</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort5">
	<option value="">Sort By 5th</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
<input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px; "/>


<hr>
<div style="max-height: calc(96.2vh - 96.2px);overflow:auto;">
	<div class="table" style="width: device-width;">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        	  <div class="cell" style="width: device-width;">
                No.
              </div>
              <div class="cell" style="width: device-width;">
                HIP
              </div>
              <div class="cell" style="width: device-width;">
                Horse
              </div>
              <div class="cell" style="width: device-width;">
                Type
              </div>
              <div class="cell" style="width: device-width;">
                Sire
              </div>
              <div class="cell" style="width: device-width;">
                Dam
              </div>
              <div class="cell" style="width: device-width;">
                Sex
              </div>
              <div class="cell" style="width: device-width;">
                Datefoal
              </div>
              <div class="cell" style="width: device-width;">
                Price
              </div>
              <div class="cell" style="width: device-width;">
                Curr
              </div>
              <div class="cell" style="width: device-width;">
                SaleCode
              </div>
              <div class="cell" style="width: device-width;">
                DS
              </div>
              <div class="cell" style="width: device-width;">
                Buyer LastName
              </div>
              <div class="cell" style="width: device-width;">
                Buyer FirstName
              </div>
          </div>
          
          <?php
            setlocale(LC_MONETARY,"en_US");
            $number =0;  
            foreach($resultFound as $row) {
                //echo print_r($buyersData);
                $number = $number+1;
                $elementCount = 0;
                echo "<div class='row'>";
                echo "<div class='cell'>".$number."</div>";
                foreach($row as $elements) {
                    $elementCount =$elementCount+1;
                    if($elementCount == 8){
                        $elements = "$".number_format($elements);
                    }
                    echo "<div class='cell'>".$elements."</div>";
                }
                echo "</div>";
            }
          ?>
    </div>
</div>
</div>

<script>
	document.getElementById('year').value="<?php echo $year_param;?>";
	document.getElementById('salecode').value="<?php echo $salecode_param;?>";
	document.getElementById('type').value="<?php echo $type_param;?>";
	document.getElementById('sort1').value="<?php echo $sort1_param;?>";
	document.getElementById('sort2').value="<?php echo $sort2_param;?>";
	document.getElementById('sort3').value="<?php echo $sort3_param;?>";
	document.getElementById('sort4').value="<?php echo $sort4_param;?>";
	document.getElementById('sort5').value="<?php echo $sort5_param;?>";
	
</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
	var salecode = document.getElementById('salecode').value;
	var type = document.getElementById('type').value;
	var sort1 = document.getElementById('sort1').value;
	var sort2 = document.getElementById('sort2').value;
	var sort3 = document.getElementById('sort3').value;
	var sort4 = document.getElementById('sort4').value;
	var sort5 = document.getElementById('sort5').value;

    var link ="buyers_report_tb.php?year="+year
    							+"&salecode="+salecode
    							+"&type="+type
    							+"&sort1="+sort1
    							+"&sort2="+sort2
    							+"&sort3="+sort3
    							+"&sort4="+sort4
    							+"&sort5="+sort5;
    //alert(link);
    							
  	window.open(link,"_self");
  	
}
</script>
