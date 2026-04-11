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

<style>
table, th, td {
  border: 1px solid white;
}
</style>
</head>
<?php
include_once("config.php");
$year_param =$_GET['year'] ?? '';
$type_param =$_GET['type'] ?? '';
$salecode_param =$_GET['salecode'] ?? '';

$resultFound = fetchSalesAuctionReport($year_param,$type_param,$salecode_param);

// Check if any parameter is selected
if (!empty($year_param) || !empty($elig_param) || !empty($salecode_param)) {
  // Call the function to fetch filtered data based on the selected parameters
$resultFound21 = fetchSalesSummary($year_param,$type_param,$salecode_param);
} else {
  // If no parameter is selected, set $resultFound to an empty array
  $resultFound = array();
}

$yearList = getYearsList();
$typeList = fetchTypeList();
$resultList = fetchSalecodeWithoutYear($year_param);
?>
<br>


<div class="container">
<h1 style="text-align:center;color:#D98880;">AUCTION REPORT</h1>

<b>
<button class="custom-select1" style="background-color:#35CC03;" onclick="downloadData();">Export Data to CSV</button>
</b>
<br>
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
	<option value="B">B : Broodmare</option>
	<option value="LB">LB : Lifetime Breeding</option>
	<option value="M">M : Maiden Broodmare</option>
	<option value="P">P : Broodmare Prospect</option>
	<option value="R">R : Race Horse</option>
	<option value="S">S : Share</option>
	<option value="SEA">SEA : Season</option>
	<option value="T">T : Stallion</option>
	<option value="W">W : Weanling</option>
	<option value="Y">Y : Yearling</option>
	<?php 
    //foreach($typeList as $row) {
// 	    echo '<option>'.$row[Type].'</option>';
//     } 
    ?>
</select>

<input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px; "/>

<hr>
  <div style="height:500px;overflow:auto;">
	<div class="table" style="width: 100%;table-layout: fixed;">
          <div class="row header" style="line-height: 25px;font-size: 12px;border: 1px solid white;position: sticky;top: 0;">
        	  <div class="cell" style="width: 3%;">
                NO
              </div>
              <div class="cell" style="width: 15%;">
                SALECODE
              </div>
              <div class="cell" style="width: 5%;">
                TOTAL
              </div>
              <div class="cell" style="width: 10%;">
                GROSS
              </div>
              <div class="cell" style="width: 10%;">
                AVERAGE
              </div>
              <div class="cell" style="width: 10%;">
                $100,000&OVER
              </div>
              <div class="cell" style="width: 10%;">
                $50,000-$99,999
              </div>
              <div class="cell" style="width: 10%;">
                $25,000-$49,999
              </div>
              <div class="cell" style="width: 10%;">
                $10,001-$24,999
              </div>
              <div class="cell" style="width: 10%;">
                $5,000-$10,000
              </div>
              <div class="cell" style="width: 10%;">
                $4,999 & UNDER
              </div>
          </div>
          
          <?php
              setlocale(LC_MONETARY,"en_US");
              $number =0;
              foreach($resultFound as $row) {
                  $number = $number+1;
                  $elementCount = 0;
                  echo "<div class='row style='font-size:15px;border: 1px solid white;'>";
                  echo "<div class='cell' style='font-size:13px;border: 1px solid white;'>".$number."</div>";
                  foreach($row as $elements) {
                      $elementCount =$elementCount+1;
                      if($elementCount == 1){
                          echo "<div class='cell' style='font-size:14px;border: 1px solid white;'><B>".$elements."</B></div>";
                          continue;
                      }
                      if($elementCount == 3 or $elementCount == 4){
                          $elements = "$".number_format($elements);
                      }
                      if ($elementCount > 4 and $elements != "") {
                          $elements = $elements."%";
                      }
                      echo "<div class='cell' style='font-size:14px;border: 1px solid white;'>".$elements."</div>";
                  }
                  echo "</div>";
              }
          ?>
    </div>
 </div>
 
 <h1 style="text-align:center;color:#D98880;">AUCTION REPORT - MAX & AVG SALES</h1>
 <hr>
 <div style="height:500px;overflow:auto;">
	<div class="table" style="width: 100%;table-layout: fixed;">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;border: 1px solid white;position: sticky;top: 0;">
        	  <div class="cell" style="width: 5%;">
                NO
              </div>
              <div class="cell" style="width: 15%;">
                SALECODE
              </div>
              <div class="cell" style="width: 40%;">
                TOP-PRICE PACER
              </div>
              <div class="cell" style="width: 40%;">
                TOP-PRICE TROTTER
              </div>
          </div>
          
          <?php
              setlocale(LC_MONETARY,"en_US");
              $number =0;
              $var ="";
              foreach($resultFound21 as $row) {
                  $number = $number+1;
                  $elementCount = 0;
                  echo "<div class='row style='font-size:14px;border: 1px solid white;'>";
                  echo "<div class='cell' style='font-size:12px;border: 1px solid white;'>".$number."</div>";
                  foreach($row as $elements) {
                      $elementCount =$elementCount+1;
                      if($elementCount == 2){
                          $var =$elements;
                          continue;
                      }
                      if($elementCount == 3){
                          $elements = "$".number_format($elements);
                          $elements = "<b>".$elements."</b> - ".$var;
                      }
                      if($elementCount == 4){
                          $var =$elements;
                          continue;
                      }
                      if($elementCount == 5){
                          $elements = "$".number_format($elements);
                          $elements = "<b>".$elements."</b> - ".$var;
                      }
                      echo "<div class='cell' style='font-size:13px;border: 1px solid white;'>".$elements."</div>";
                  }
                  echo "</div>";
              }
          ?>
    </div>
 </div>
  <br>
</div>
<br>
<script>
	document.getElementById('year').value="<?php echo $year_param;?>";
	document.getElementById('type').value="<?php echo $type_param;?>";
	document.getElementById('salecode').value="<?php echo $salecode_param;?>";
	
</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
    var type = document.getElementById('type').value;
    var salecode = document.getElementById('salecode').value;

    var link ="auction_report.php?year="+year
    						     +"&type="+type
    						     +"&salecode="+salecode;
    window.open(link,"_self");
    //alert(link);
}
function downloadData(){
	var year = document.getElementById('year').value;
    var type = document.getElementById('type').value;
    var salecode = document.getElementById('salecode').value;

    var link ="auction_report_download.php?year="+year
    						     +"&type="+type
    						     +"&salecode="+salecode;
    window.open(link,"_blank");
}
</script>
