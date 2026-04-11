<?php
include("./header.php");
include("./session_page.php");
?>

<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/table.css">
   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
  <script src="assets/js/script.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<?php
include_once("config.php");
$salecode_param = $_GET['salecode'] ?? '';
$year_param = $_GET['year'] ?? '';
$type_param = $_GET['type'] ?? '';
$sex_param = $_GET['sex'] ?? '';
$sire_param = $_GET['sire'] ?? '';

$sort1_param = $_GET['sort1'] ?? '';
$sort2_param = $_GET['sort2'] ?? '';
$sort3_param = $_GET['sort3'] ?? '';
$sort4_param = $_GET['sort4'] ?? '';
$sort5_param = $_GET['sort5'] ?? '';

$sort1_param_order = isset($_GET['sort1_order']) ? $_GET['sort1_order'] : ''; // default to 'ASC' if not set
$sort2_param_order = isset($_GET['sort2_order']) ? $_GET['sort2_order'] : ''; // default to 'ASC' if not set
$sort3_param_order = isset($_GET['sort3_order']) ? $_GET['sort3_order'] : ''; // default to 'ASC' if not set
$sort4_param_order = isset($_GET['sort4_order']) ? $_GET['sort4_order'] : ''; // default to 'ASC' if not set
$sort5_param_order = isset($_GET['sort5_order']) ? $_GET['sort5_order'] : ''; // default to 'ASC' if not set

$resultFound = breezeFromYearlingReport_tb($year_param, $salecode_param, $type_param, $sex_param, $sire_param, $sort1_param, $sort2_param, $sort3_param, $sort4_param, $sort5_param);

$yearList = getYearsList_tb_breeze();
$resultList = fetchSalecodeList_tb1($year_param);
$typeList = fetchTypeList_tb();
$sexList = getSexList_tb();
$sireList = fetchSireListAll_tb($year_param);

$sortList = array("Sire", "Dam", "Hip", "Sex", "utt", "Price");

?>
<br>


<div style= "margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">BREEZE FROM YEARLING REPORT THOROUGHBRED</h1>
<!-- <b> -->
<!-- <button class="custom-select1" style="background-color:#35CC03;" onclick="downloadData();">Export Data to CSV</button> -->
<!-- </b> -->
<br>
<br>
<select class="custom-select1" id="year" onchange="updateSalecode_tb(this.value)">
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
	<option value="Y">Y : Yearling</option>
  <option value="R">R : Racing Horse</option>
  <script>
    $(document).ready(function() {
    $('#type').val('R'); 
    });
  </script>
	<?php 
    //foreach($typeList as $row) {
// 	    echo '<option>'.$row[Type].'</option>';
//     } 
    ?>
</select>
<select class="custom-select1" id="sex"> 
	<option value="">Sex Filter</option>
	<option value="">All Sex</option>
	<?php foreach($sexList as $row) {
  	    echo '<option>'.$row['Sex'].'</option>';
    } ?>
</select>
<select class="custom-select1" id="sire"> 
	<option value="">Sire Filter</option>
	<option value="">All Sire</option>
	<?php foreach($sireList as $row) {
  	    echo '<option>'.$row['Sire'].'</option>';
    } ?>
</select>

<!-- Sorting Filters (1st to 5th) -->
<select style="background-color:#229954;" class="custom-select1" id="sort1" name="sort1" onchange="updateSortOrder('sort1')">
    <option value="">Sort By 1st</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort1_order" onchange="updateSortOrder('sort1')">
  <option value="">Select Order</option> <!-- Default option -->
  <option value="ASC" <?php echo ($sort1_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
  <option value="DESC" <?php echo ($sort1_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort2" name="sort2" onchange="updateSortOrder('sort2')">
    <option value="">Sort By 2nd</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort2_order" onchange="updateSortOrder('sort2')">
  <option value="">Select Order</option> <!-- Default option -->
  <option value="ASC" <?php echo ($sort2_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
  <option value="DESC" <?php echo ($sort2_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort3" name="sort3" onchange="updateSortOrder('sort3')">
    <option value="">Sort By 3rd</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort3_order" onchange="updateSortOrder('sort3')">
  <option value="">Select Order</option> <!-- Default option -->
  <option value="ASC" <?php echo ($sort3_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
  <option value="DESC" <?php echo ($sort3_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort4" name="sort4" onchange="updateSortOrder('sort4')">
    <option value="">Sort By 4th</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort4_order" onchange="updateSortOrder('sort4')">
  <option value="">Select Order</option> <!-- Default option -->
  <option value="ASC" <?php echo ($sort4_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
  <option value="DESC" <?php echo ($sort4_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option></select>

<select style="background-color:#229954;" class="custom-select1" id="sort5" name="sort5" onchange="updateSortOrder('sort5')">
    <option value="">Sort By 5th</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort5_order" onchange="updateSortOrder('sort5')">
  <option value="">Select Order</option> <!-- Default option -->
  <option value="ASC" <?php echo ($sort5_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
  <option value="DESC" <?php echo ($sort5_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>

<input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px; "/>

<script>
    // Function to toggle sorting order between ASC and DESC
  function updateSortOrder(sortField) {
    // Get the selected value from the sort dropdown
    let sortValue = document.getElementById(sortField).value;

    // Get the selected order (ASC or DESC) from the corresponding dropdown
    let orderValue = document.getElementById(sortField + '_order').value;

    // Update the hidden input fields with the selected values
    document.getElementById(sortField + '_order').value = orderValue;

    console.log(`Sorting by ${sortValue} in ${orderValue} order`);
  }
</script>

<hr>
<div style="max-height: calc(96.2vh - 96.2px);overflow:auto;">
	<div class="table" style="width: device-width">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;"> 

          <div class="cell" style="width: device-width;background-color:#D98880">
                Offspring Horse
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Hip
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Sex
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Datefoal
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Salecode
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Price
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Rating 
              </div> 
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Sale Type
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Dam-Racing
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                UTT
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Sire
              </div>


              <div class="cell"  style="width: device-width;">
                No.
              </div>
              <div class="cell"  style="width: device-width;">
                Purchaser Name
              </div> 
              <div class="cell"  style="width: device-width;">
                HIP
              </div>
              <div class="cell"  style="width: device-width;">
                Datefoal
              </div>
              <div class="cell"  style="width: device-width;">
                Dam- Y or W
              </div>
              <div class="cell"  style="width: device-width;">
                Sex
              </div>
              <div class="cell"  style="width: device-width;">
                Type
              </div>
              <div class="cell"  style="width: device-width;">
                Price
              </div>
              <div class="cell"  style="width: device-width;">
                SaleCode
              </div>
              <div class="cell"  style="width: device-width;">
                Day
              </div>
              <div class="cell"  style="width: device-width;">
                Consno
              </div>
              <div class="cell"  style="width: device-width;">
                Sale Type
              </div>
              <div class="cell"  style="width: device-width;">
                Age
              </div>
              <div class="cell"  style="width: device-width;">
                Rating
              </div>

              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Total
              </div>
              
             

             </div>
<?php
  setlocale(LC_MONETARY,"en_US");
  $number =0;  

  // Loop through the first set of data (main horse details)
  foreach ($resultFound as $row) {
    // Retrieve offspring data (second set of data)
    $offspringRows = fetchBreezeSoldAsYearling($row['Salecode'], $row['TDAM']);
    
    // Start a row for the main horse details
    echo "<div class='row'>";

    // Display the main horse data (from the first query)
    echo "<div class='cell'>" . $row['Horse'] . "</div>";  // 1. Offspring Horse
    echo "<div class='cell'>" . $row['Hip'] . "</div>";    // 2. Hip
    echo "<div class='cell'>" . $row['Sex'] . "</div>";    // 3. Sex
    echo "<div class='cell'>" . date("m/d/y", strtotime($row['Datefoal'])) . "</div>"; // 4. Datefoal
    echo "<div class='cell'>" . $row['Salecode'] . "</div>"; // 5. Salecode
    echo "<div class='cell'>" . "$" . number_format($row['Price'], 0) . "</div>"; // 6. Price
    echo "<div class='cell'>" . $row['Rating'] . "</div>"; // 7. Rating
    echo "<div class='cell'>" . $row['b_type'] . "</div>";  // 8. Sale Type
    echo "<div class='cell'>" . $row['TDAM'] . "</div>";  // 9. Dam-R
    echo "<div class='cell'>" . $row['utt'] . "</div>"; // 10. utt
    echo "<div class='cell'>" . $row['tSire'] . "</div>";  // 11. Sire


    // Now, loop through the offspring details (second query)
    // and append their data in the same row
    foreach ($offspringRows as $offspringRow) {
        // Display offspring data in the same row as the main horse
        echo "<div class='cell'>" . $number++ . "</div>";  // 12. No.
        echo "<div class='cell'>" . $offspringRow['Purlname'] . ' ' . $offspringRow['Purfname'] . "</div>"; // 13. Purchaser Name
        echo "<div class='cell'>" . $offspringRow['HIP'] . "</div>"; // 14. HIP
        echo "<div class='cell'>" . date("m/d/y", strtotime($offspringRow['Datefoal'])) . "</div>"; // 15. Datefoal
        echo "<div class='cell'>" . $offspringRow['Dam'] . "</div>"; // 16. Dam
        echo "<div class='cell'>" . $offspringRow['Sex'] . "</div>"; // 17. Sex
        echo "<div class='cell'>" . $offspringRow['type'] . "</div>"; // 18. Type
        echo "<div class='cell'>" . "$" . number_format($offspringRow['Price'], 0) . "</div>"; // 19. Price
        echo "<div class='cell'>" . $offspringRow['Salecode'] . "</div>"; // 20. SaleCode
        echo "<div class='cell'>" . $offspringRow['Day'] . "</div>"; // 21. Day
        echo "<div class='cell'>" . $offspringRow['Consno'] . "</div>"; // 22. Consno
        echo "<div class='cell'>" . $offspringRow['saletype'] . "</div>"; // 23. Sale Type
        echo "<div class='cell'>" . $offspringRow['Age'] . "</div>"; // 24. Age
        echo "<div class='cell'>" . $offspringRow['Rating'] . "</div>"; // 25. Rating

        // Calculate and display price difference for each offspring
        $priceDifference = $row['Price'] - $offspringRow['Price'];
        $cellColor = ($priceDifference < 0) ? '#FF6347' : '#32CD32';
        echo "<div class='cell' style='background-color:$cellColor'>" . "$" . number_format($priceDifference, 0) . "</div>"; // 26. Total
    }
    
    // Close the row for this horse entry (main data + offspring data)
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
	document.getElementById('sex').value="<?php echo $sex_param;?>";
	document.getElementById('sire').value="<?php echo $sire_param;?>";
	document.getElementById('sort1').value="<?php echo $sort1_param;?>";
	document.getElementById('sort2').value="<?php echo $sort2_param;?>";
	document.getElementById('sort3').value="<?php echo $sort3_param;?>";
	document.getElementById('sort4').value="<?php echo $sort4_param;?>";
	document.getElementById('sort5').value="<?php echo $sort5_param;?>";
  document.getElementById('sort1_order').value = "<?php echo $sort1_param_order; ?>";
  document.getElementById('sort2_order').value = "<?php echo $sort2_param_order; ?>";
  document.getElementById('sort3_order').value = "<?php echo $sort3_param_order; ?>";
  document.getElementById('sort4_order').value = "<?php echo $sort4_param_order; ?>";
  document.getElementById('sort5_order').value = "<?php echo $sort5_param_order; ?>";

</script>
<script>
function getValues() {
  var year = document.getElementById('year').value;
	var salecode = document.getElementById('salecode').value;
	var type = document.getElementById('type').value;
	var sex = document.getElementById('sex').value;
	var sire = document.getElementById('sire').value;
	var sort1 = document.getElementById('sort1').value;
	var sort2 = document.getElementById('sort2').value;
	var sort3 = document.getElementById('sort3').value;
	var sort4 = document.getElementById('sort4').value;
	var sort5 = document.getElementById('sort5').value;

  // Sorting orders (ASC or DESC)
  var sort1_order = document.getElementById('sort1_order').value;
  var sort2_order = document.getElementById('sort2_order').value;
  var sort3_order = document.getElementById('sort3_order').value;
  var sort4_order = document.getElementById('sort4_order').value;
  var sort5_order = document.getElementById('sort5_order').value;


    var link ="breeze_to_yearling_report.php?year="+year
    							+"&salecode="+salecode
    							+"&type="+type
    							+"&sex="+sex
    							+"&sire="+sire
                  +"&sort1="+sort1
                  + "&sort1_order=" + sort1_order // Added sorting order
    							+"&sort2="+sort2
                  + "&sort2_order=" + sort2_order // Added sorting order
    							+"&sort3="+sort3
                  + "&sort3_order=" + sort3_order // Added sorting order
    							+"&sort4="+sort4
                  + "&sort4_order=" + sort4_order // Added sorting order
    							+"&sort5="+sort5
                  + "&sort5_order=" + sort5_order; // Added sorting order
    							
    window.open(link,"_self");
  	if(year== "" && salecode== "" && type == "" && sex== "" && sire == "")
  	{
  		alert("Please Select Atleast One Category Filter");
  	}
}

</script>

