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
$consno_param =$_GET['consno'];
$year_param =$_GET['year'];
$elig_param =$_GET['elig'];
$gait_param =$_GET['gait'];
$sort1_param =$_GET['sort1'];
$sort2_param =$_GET['sort2'];
$sort3_param =$_GET['sort3'];
$sort4_param =$_GET['sort4'];
$sort5_param =$_GET['sort5'];

$resultFound = fetchConsAnalysis($consno_param,$year_param,$elig_param,$gait_param);
$resultList = fetchConsnoList($year_param);
$yearList = getYearsList();
$eligList = getEligList();
$gaitList = getGaitList();

$sortList = array("Rank","FRank","CRank","Gait","SaleDate","Day","SaleCode", "Dam","Sireofdam", 
                  "Sex","Color","Type", "Elig", "Hip", "Price Desc", "ConsNo","Purlname","Purfname");

echo "<br>";

echo '<div style= "margin:5px 30px 30px 30px;">';
echo '<h1 style="text-align:center;color:#D98880;">CONSUMER ANALYSIS   
<label style="color:5D6D7E";>'.$year_param.'</label> </h1>';
?>
<select class="custom-select1" id="year"> <!--onchange="location = this.value;" -->
	<option value="">Sale Year</option>
	<option  value="">All Years</option>
	<?php foreach($yearList as $row) {
	    echo '<option>'.$row['Year'].'</option>';
    } ?>
</select>
 <select class="custom-select1" id="consno"> <!--onchange="location = this.value;" -->
	<option value="">Consno Filter</option>
	<option value="">All Consno</option>
	<?php foreach($resultList as $row) {
  	    echo '<option>'.$row['Consno'].'</option>';
    } ?>
</select>
<select class="custom-select1" id="elig"> 
	<option value="">Elig Filter</option>
	<option  value="">All Elig</option>
	<?php foreach($eligList as $row) {
  	    echo '<option>'.$row['Elig'].'</option>';
    } ?>
</select>
<select class="custom-select1" id="gait"> 
	<option value="">Gait Filter</option>
	<option value="">All Gait</option>
	<?php foreach($gaitList as $row) {
  	    echo '<option>'.$row['Gait'].'</option>';
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
    		    echo '<h4 style="color:#E74C3C";><B><u>'.$row['Consno'].'</u></B></h4>';
    		    echo '<h5><B>Total Sold - <label style="color:#E74C3C";>'.$row['Count'].'</label> | 
                        Total- <label style="color:#E74C3C";>$'.number_format($row['Total']).'</label> | 
                        Average - <label style="color:#E74C3C";>$'.number_format($row['Avg']).'</label> | 
                        Top Seller - <label style="color:#E74C3C";>$'.number_format($row['Top']).'</label></B></h5>';
    		    
    		    echo '</div>';
    		    
if ($year_param != "" or $consno_param != "" or $elig_param != "" or $gait_param != "") {
?>
       <div class="table" style="width: 100%;table-layout: fixed;">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        	  <div class="cell" style="width: 2%;">
                No.
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
              <div class="cell" style="width: 2%;">
                ET
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
              <div class="cell" style="width: 3%;">
                Rate
              </div>
          </div>
          
          <?php
		    
		    #$lastname1 =$row[Sire];
            $number =0;
            $consnoData = fetchConsnoData($row['Consno'],$year_param,$elig_param,$gait_param,$sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);         

            foreach($consnoData as $row1) {
                  $elementCount = 0;
                  $number = $number+1;
                  
                  echo "<div class='row'>";
                  echo "<div class='cell'>".$number."</div>";
                  //echo "<div class='cell'>".$number."</div>";
                  #echo "</a>";
                  #echo $row[Price];
                  foreach($row1 as $elements) {
                      $elementCount =$elementCount+1;
                      if($elementCount == 16){
                          $elements = "$".number_format($elements);
                      }
                      if ($elements == "0000-00-00") {
                          $elements="";
                      }
                      if ($elementCount == 8 or $elementCount == 14) {
                          if ($elements != "") {
                              $date=date_create($elements);
                              $elements = date_format($date,"m/d/y");
                          }
                      }
                      if ($elementCount == 13) {
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
	document.getElementById('consno').value="<?php echo $consno_param;?>";
	document.getElementById('elig').value="<?php echo $elig_param;?>";
	document.getElementById('gait').value="<?php echo $gait_param;?>";
	document.getElementById('sort1').value="<?php echo $sort1_param;?>";
	document.getElementById('sort2').value="<?php echo $sort2_param;?>";
	document.getElementById('sort3').value="<?php echo $sort3_param;?>";
	document.getElementById('sort4').value="<?php echo $sort4_param;?>";
	document.getElementById('sort5').value="<?php echo $sort5_param;?>";

</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
	var consno = document.getElementById('consno').value;
	var elig = document.getElementById('elig').value;
	var gait = document.getElementById('gait').value;
	var sort1 = document.getElementById('sort1').value;
	var sort2 = document.getElementById('sort2').value;
	var sort3 = document.getElementById('sort3').value;
	var sort4 = document.getElementById('sort4').value;
	var sort5 = document.getElementById('sort5').value;

    var link ="cons_analysis.php?year="+year
    							+"&consno="+consno
    							+"&elig="+elig
    							+"&gait="+gait
    							+"&sort1="+sort1
    							+"&sort2="+sort2
    							+"&sort3="+sort3
    							+"&sort4="+sort4
    							+"&sort5="+sort5;
    //alert(link);
    							
  	window.open(link,"_self");
  	
}
</script>


