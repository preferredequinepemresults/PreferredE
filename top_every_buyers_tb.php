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
$year_param =$_GET['year'];
$sort1_param =$_GET['sort1'];
$sort2_param =$_GET['sort2'];
$sort3_param =$_GET['sort3'];
$sort4_param =$_GET['sort4'];
$sort5_param =$_GET['sort5'];

$resultFound = fetchAllTopBuyers_tb($year_param,$sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);

$yearList = getYearsList_tb();

$sortList = array("BuyerFullName","Total Asc", "Total Desc", "Gross Asc", "Gross Desc",
                  "Avg Asc", "Avg Desc");
?>
<br>


<div class="container">
<h1 style="text-align:center;color:#D98880;">THOROUGHBRED ALL TOP BUYERS</h1>


<select class="custom-select1" id="year">
	<option value=null>Sale Year</option>
	<option value=null>All Years</option>
	<?php foreach($yearList as $row) {
	    echo '<option>'.$row['Year'].'</option>';
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


<hr>
<div style="max-height: calc(96.2vh - 96.2px);overflow:auto;">
	<div class="table" style="width: device-width;">
          <div class="row header green" style="line-height: 25px;font-size: 12px;border: 1px solid black;position: sticky;top: 0;">
        	  <div class="cell" style="width: device-width;">
                NO
              </div>
              <div class="cell" style="width: device-width;">
                HIP OF HORSES BOUGHT
              </div>
              <div class="cell" style="width: device-width;">
                BUYER FULL NAME
              </div>
              <div class="cell" style="width: device-width;">
                TOTAL NO PURCHASED
              </div>
              <div class="cell" style="width: device-width;">
                GROSS
              </div>
              <div class="cell" style="width: device-width;">
                AVERAGE
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

                  $collapseID = "collapse" . $number;
  
                  echo "<div class='cell'>";
                  echo "<button class='btn btn-link' type='button' data-toggle='collapse' data-target='#$collapseID' aria-expanded='false' aria-controls='$collapseID'>";
                  echo "HIP OF HORSES BOUGHT" . "</button>"; // HIP button for collapsing
                  echo "</div>";

                  foreach($row as $key => $elements) {
                      if ($key === 'Hips') {
                        continue; // Skip Horse in the main row display
                      }

                      $elementCount =$elementCount+1;
                      
                      if($elementCount == 4 || $elementCount == 3){
                        $elements = "$".number_format(floatval($elements), 0);
                      }
                      echo "<div class='cell' style='font-size:14px;border: 1px solid white;'>".$elements."</div>";
                  }
                  echo "</div>";

                  // Otherwise, display each horse individually in a collapsible panel
                  echo "<div id='$collapseID' class='collapse' style='padding: 0; margin: 0; background-color: #d3d3d3;'>";
                  echo "<div class='cell' style='padding-left: 20px;'><i>Hip of Horses Bought:</i> <i>" . $row['Hips'] . "</i></div>";
                  echo "</div>"; // End of collapsible panel
                }
              
          ?>
    </div>
 </div>
 
 <hr>
  <br>
</div>

<script>
if("<?php echo $year_param;?>" != "")
	{
	document.getElementById('year').value="<?php echo $year_param;?>";
	}
	document.getElementById('sort1').value="<?php echo $sort1_param;?>";
	document.getElementById('sort2').value="<?php echo $sort2_param;?>";
	document.getElementById('sort3').value="<?php echo $sort3_param;?>";
	document.getElementById('sort4').value="<?php echo $sort4_param;?>";
	document.getElementById('sort5').value="<?php echo $sort5_param;?>";
	
</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
	var sort1 = document.getElementById('sort1').value;
	var sort2 = document.getElementById('sort2').value;
	var sort3 = document.getElementById('sort3').value;
	var sort4 = document.getElementById('sort4').value;
	var sort5 = document.getElementById('sort5').value;
	
    var link ="top_every_buyers_tb.php?year="+year
    							+"&sort1="+sort1
    							+"&sort2="+sort2
    							+"&sort3="+sort3
    							+"&sort4="+sort4
    							+"&sort5="+sort5;;
    window.open(link,"_self");
    //alert(link);
}
</script>
