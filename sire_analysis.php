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
$sire_param = isset($_GET['sire']) ? $_GET['sire'] : '';
$year_param = isset($_GET['year']) ? $_GET['year'] : '';
$elig_param = isset($_GET['elig']) ? $_GET['elig'] : '';
$gait_param = isset($_GET['gait']) ? $_GET['gait'] : '';
$salecode_param = isset($_GET['salecode']) ? $_GET['salecode'] : '';
$sort1_param = isset($_GET['sort1']) ? $_GET['sort1'] : '';
$sort2_param = isset($_GET['sort2']) ? $_GET['sort2'] : '';
$sort3_param = isset($_GET['sort3']) ? $_GET['sort3'] : '';
$sort4_param = isset($_GET['sort4']) ? $_GET['sort4'] : '';
$sort5_param = isset($_GET['sort5']) ? $_GET['sort5'] : '';

$resultFound = fetchSireAnalysis($sire_param,$year_param,$elig_param,$gait_param, $salecode_param);
$resultList = fetchSireList($year_param);
$yearList = getYearsList();
$eligList = getEligList();
//$gaitList = getGaitList_tb();
$salcodeList = fetchSalecodeList($year_param);


$sortList = array("Rank","FRank","CRank","SaleDate","Day","SaleCode", "Dam","Sireofdam", 
                  "Sex","Color","Type", "Elig", "Hip", "Price Desc", "ConsNo","Purlname","Purfname");

echo "<br>";

echo '<div style= "margin:5px 30px 30px 30px;">';
echo '<h1 style="text-align:center;">SIRE ANALYSIS   
<label style="color:5D6D7E";>'.$year_param.'</label> <label style="color:#D98880";>'.$sire_param.'</label></h1>';
?>
<select class="custom-select1" id="year" onchange="updateSalecode(this.value)"> <!--onchange="location = this.value;" -->
	<option value="">Sale Year</option>
	<option  value="">All Years</option>
	<?php foreach($yearList as $row) {
	    echo '<option>'.$row['Year'].'</option>';
    } ?>
</select>
<!-- Replace the select with input + datalist -->
<input class="custom-select1" list="sire-options" id="sire" name="sire" placeholder="Sire Filter" autocomplete="off">
<datalist id="sire-options">
    <option value="">All Sire</option>
    <?php foreach($resultList as $row) {
        echo '<option value="'.htmlspecialchars($row['Sire']).'">';
    } ?>
</datalist>

<select class="custom-select1" id="elig"> 
	<option value="">Elig Filter</option>
	<option  value="">All Elig</option>
	<?php foreach($eligList as $row) {
  	    echo '<option>'.$row['Elig'].'</option>';
    } ?>
</select>

<select class="custom-select1" id="salecode"> <!--onchange="location = this.value;" -->
    <option value="">Salecode Filter</option>
    <option value="">All Salecode</option>
    <?php foreach ($salcodeList as $row) {
        echo '<option>'.$row['Salecode'].'</option>';

    } ?>
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
<div style="height:810px;overflow:auto;">
<hr>
<?php
        setlocale(LC_MONETARY,"en_US");
		foreach($resultFound as $row) {
    		    echo '<div class="sire-header">';
    		    echo '<h4 style="color:#E74C3C";><B><u>'.$row['Sire'].'</u></B></h4>';
    		    echo '<h5><B>Total Sold - <label style="color:#E74C3C";>'.$row['Count'].'</label> | 
                        Total- <label style="color:#E74C3C";>$'.number_format($row['Total']).'</label> | 
                        Average - <label style="color:#E74C3C";>$'.number_format($row['Avg']).'</label> | 
                        Top Seller - <label style="color:#E74C3C";>$'.number_format($row['Top']).'</label></B></h5>';
    		    echo '<h5><B>Colts Sold - <label style="color:#E74C3C";>'.$row['CCount'].'</label> | 
                        Colts Total- <label style="color:#E74C3C";>$'.number_format($row['CTotal']).'</label> | 
                        Colts Average - <label style="color:#E74C3C";>$'.number_format($row['CAvg']).'</label> | 
                        Colts Top Seller - <label style="color:#E74C3C";>$'.number_format($row['CTop']).'</label>
                        </B></h5>
                        <h5><B>Fillies Sold - <label style="color:#E74C3C";>'.$row['FCount'].'</label> | 
                        Fillies Total- <label style="color:#E74C3C";>$'.number_format($row['FTotal']).'</label> | 
                        Fillies Average - <label style="color:#E74C3C";>$'.number_format($row['FAvg']).'</label> | 
                        Fillies Top Seller - <label style="color:#E74C3C";>$'.number_format($row['FTop']).'</label></B></h5>';
    		    echo '</div>';
    		    
if ($year_param != "" or $sire_param != "" or $elig_param != "") {
?>
       <div class="table" style="width: 100%;overflow: auto;">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        	  <div class="cell" style="width: 2%;">
                No.
              </div>
              <div class="cell" style="width: 2%;">
                R
              </div>
              <div class="cell" style="width: 2%;">
                FR
              </div>
              <div class="cell" style="width: 2%;">
                CR
              </div>
              <div class="cell" style="width: 3%;">
                HIP
              </div>
              <div class="cell" style="width: 12%;">
                Horse
              </div>
              <div class="cell" style="width: 2%;">
                S
              </div>
              <div class="cell" style="width: 2%;">
                C
              </div>
              <div class="cell" style="width: 2%;">
                G
              </div>
              <div class="cell" style="width: 2%;">
                T
              </div>
              <div class="cell" style="width: 5%;">
                DOB
              </div>
              <div class="cell" style="width: 3%;">
                Elig
              </div>
              <div class="cell" style="width: 12%;">
                Dam
              </div>
              <div class="cell" style="width: 12%;">
                SireOfDam
              </div>
              <div class="cell" style="width: 10%;">
                SaleCode
              </div>
              <div class="cell" style="width: 4%;">
                Consno
              </div>
              <div class="cell" style="width: 5%;">
                Saledate
              </div>
              <div class="cell" style="width: 2%;">
                Day
              </div>
              <div class="cell" style="width: 5%;">
                Price
              </div>
              <div class="cell" style="width: 3%;">
                Curr
              </div>
              <div class="cell" style="width: 7%;">
                PLastName
              </div>
              <div class="cell" style="width: 7%;">
                PFirstName
              </div>
              <div class="cell" style="width: 1.5%;">
                Rate
              </div>
          </div>
          
          <?php
		    
		    #$lastname1 =$row[Sire];
            $number =0;
            $sireData = fetchSireData($row['Sire'],$year_param,$elig_param,$gait_param,$sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param,$salecode_param);         

            foreach($sireData as $row1) {
                  $elementCount = 0;
                  $number = $number+1;
                  
                  echo "<div class='row'>";
                  echo "<div class='cell'>".$number."</div>";
                  //echo "<div class='cell'>".$number."</div>";
                  #echo "</a>";
                  #echo $row[Price];
                  foreach($row1 as $elements) {
                      $elementCount =$elementCount+1;
                      if($elementCount == 18){
                          $elements = "$".number_format((float)$elements);
                      }
                      if ($elements == "0000-00-00") {
                          $elements="";
                      }
                      if ($elementCount == 10 or $elementCount == 16) {
                        if ($elements != "" && $elements !== "1900-01-01") {
                          $date = DateTime::createFromFormat('Y-m-d', $elements);
                          if ($date !== false && $date instanceof DateTime) {
                            $elements = $date->format('Y-m-d');
                          } else {
                            // Handle invalid date format here
                            error_log("Invalid date format: $elements");
                            $elements = ""; // Default value for invalid date
                          }
                        }
                      }

                      if ($elementCount == 15) {
                          $elements= substr($elements, 0,4);
                      }
                      
                      echo "<div class='cell'>".$elements."</div>";
                      
                  }echo "</div>";
            }
                  echo "</div>";
              }
		}
          ?>
</div>
</div>
<br>
<script>
	document.getElementById('year').value="<?php echo $year_param;?>";
	document.getElementById('sire').value="<?php echo $sire_param;?>";
	document.getElementById('elig').value="<?php echo $elig_param;?>";
  document.getElementById('salecode').value="<?php echo $salecode_param;?>";
	document.getElementById('sort1').value="<?php echo $sort1_param;?>";
	document.getElementById('sort2').value="<?php echo $sort2_param;?>";
	document.getElementById('sort3').value="<?php echo $sort3_param;?>";
	document.getElementById('sort4').value="<?php echo $sort4_param;?>";
	document.getElementById('sort5').value="<?php echo $sort5_param;?>";

</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
	var sire = document.getElementById('sire').value;
	var elig = document.getElementById('elig').value;
  var salecode = document.getElementById('salecode').value;
	var sort1 = document.getElementById('sort1').value;
	var sort2 = document.getElementById('sort2').value;
	var sort3 = document.getElementById('sort3').value;
	var sort4 = document.getElementById('sort4').value;
	var sort5 = document.getElementById('sort5').value;

    var link ="sire_analysis.php?year="+year
    							+"&sire="+sire
    							+"&elig="+elig
                  +"&salecode="+salecode
    							+"&sort1="+sort1
    							+"&sort2="+sort2
    							+"&sort3="+sort3
    							+"&sort4="+sort4
    							+"&sort5="+sort5;
    //alert(link);
    							
  	window.open(link,"_self");
  	
}
</script>