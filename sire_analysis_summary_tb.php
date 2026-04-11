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
table{
overflow-y:scroll;
   height:10px;
   display:block;
  border: 1px solid white;
}
table, th, td {
  border: 1px solid white;
}
</style>
</head>
<?php

include_once("config.php");
$year_param =$_GET['year'];
$elig_param =$_GET['elig'];
$sort1_param =$_GET['sort1'];
$sort2_param =$_GET['sort2'];
$sort3_param =$_GET['sort3'];
$sort4_param =$_GET['sort4'];
$sort5_param =$_GET['sort5'];

$resultFound = fetchSireAnalysisSummary_tb($year_param,$elig_param,$sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);
$yearList = getYearsList_tb();
$eligList = getEligList_tb();

$sortList = array("Horse","Elig","SireAvgRank", "SireGrossRank");

echo "<br>";

echo '<div style= "margin:5px 30px 30px 30px;">';
echo '<h1 style="text-align:center;color:#1C2833;">THOROUGHBRED SIRE ANALYSIS SUMMARY   
<label style="color:5D6D7E";>'.$year_param.'</label> <label style="color:#D98880";>'.$elig_param.'</label></h1>';
?>
<select class="custom-select1" id="year">
	<option value="">Sale Year</option>
	<option value="">All Years</option>
	<?php foreach($yearList as $row) {
	    echo '<option>'.$row['Year'].'</option>';
    } ?>
</select>
<select class="custom-select1" id="elig"> 
	<option value="">Elig Filter</option>
	<option value="">All Elig</option>
	<?php foreach($eligList as $row) {
  	    echo '<option>'.$row['Elig'].'</option>';
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
  <div style="height:810px;overflow:auto;">
       <table class="table" style="width: 100%;table-layout: fixed;">
          <tr class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        	  <td class="cell" rowspan="2" style="width: 2%;">
                NO
              </td>
              <td class="cell" rowspan="2" style="width: 15%;">
                HORSE
              </td>
              <td class="cell" rowspan="2" style="width: 3%;">
                ELIG
              </td>
              <td class="cell" colspan="4" style="width: 20%;text-align:center;">
                SIRES
              </td>
              <td class="cell" colspan="4" style="width: 20%;;text-align:center;">
                COLTS
              </td>
              <td class="cell" colspan="4" style="width: 20%;;text-align:center;">
                FILLIES
              </td>
              <td class="cell" colspan="2" style="width: 20%;;text-align:center;">
                SIRE RANK
              </td>
          </tr>
          <tr class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 34;">   
              <td class="cell" style="width: 2px;">
                TOTAL
              </td>
              <td class="cell" style="width: 6%;">
                GROSS
              </td>
              <td class="cell" style="width: 6%;">
                AVG
              </td>
              <td class="cell" style="width: 6%;">
                MAX
              </td>
              <td class="cell" style="width: 2%;">
                TOTAL
              </td>
              <td class="cell" style="width: 6%;">
                GROSS
              </td>
              <td class="cell" style="width: 6%;">
                AVG
              </td>
              <td class="cell" style="width: 6%;">
                MAX
              </td>
              <td class="cell" style="width: 2%;">
                TOTAL
              </td>
              <td class="cell" style="width: 6%;">
                GROSS
              </td>
              <td class="cell" style="width: 6%;">
                AVG
              </td>
              <td class="cell" style="width: 6%;">
                MAX
              </td>
              <td class="cell" style="width: 10%;">
                AVG
              </td>
              <td class="cell" style="width: 10%;">
                GROSS
              </td>
          </tr>
          
          <?php
            setlocale(LC_MONETARY,"en_US");
            $number =0;  
            foreach($resultFound as $row) {
                $number = $number+1;
                $elementCount = 0;
                echo "<tr class='row' style='line-height: 15px;font-size: 10px;font-weight: 500;'>";
                echo "<td class='cell'>".$number."</td>";
                foreach($row as $elements) {
                    $elementCount =$elementCount+1;
                    
                    if($elementCount == 4 or $elementCount == 5 or $elementCount == 6 or
                        $elementCount == 8 or $elementCount == 9 or $elementCount == 10 or
                        $elementCount == 12 or $elementCount == 13 or $elementCount == 14){
                            
                            $elements = "$".number_format($elements);
                    }
                    
                    echo "<td class='cell'>".$elements."</td>";
                }
                
                echo "</tr>";
            }
          ?>
</table>
</div>
<br>
<script>
	document.getElementById('year').value="<?php echo $year_param;?>";
	document.getElementById('elig').value="<?php echo $elig_param;?>";
	document.getElementById('sort1').value="<?php echo $sort1_param;?>";
	document.getElementById('sort2').value="<?php echo $sort2_param;?>";
	document.getElementById('sort3').value="<?php echo $sort3_param;?>";
	document.getElementById('sort4').value="<?php echo $sort4_param;?>";
	document.getElementById('sort5').value="<?php echo $sort5_param;?>";

</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
	var elig = document.getElementById('elig').value;
	var sort1 = document.getElementById('sort1').value;
	var sort2 = document.getElementById('sort2').value;
	var sort3 = document.getElementById('sort3').value;
	var sort4 = document.getElementById('sort4').value;
	var sort5 = document.getElementById('sort5').value;

    var link ="sire_analysis_summary_tb.php?year="+year
    							+"&elig="+elig
    							+"&sort1="+sort1
    							+"&sort2="+sort2
    							+"&sort3="+sort3
    							+"&sort4="+sort4
    							+"&sort5="+sort5;
    //alert(link);
    							
  	window.open(link,"_self");
  	
}
</script>


