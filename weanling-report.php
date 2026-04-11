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
$salecode_param =$_GET['salecode'] ?? '';
$year_param =$_GET['year'] ?? '';
$type_param =$_GET['type'] ?? '';
$sex_param =$_GET['sex'] ?? '';
$sire_param =$_GET['sire'] ?? '';

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

$resultFound = fetchWeanlingReport($salecode_param,$year_param,$type_param,
    $sex_param,$sire_param,$sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);

    function isAllDataNull(array $data): bool
{
    if (empty($data)) {
        return true; // No rows at all
    }

    foreach ($data as $row) {
        // Check if at least one non-null value exists in this row
        $hasData = false;
        foreach ($row as $value) {
            if (!is_null($value) && $value !== '') {
                $hasData = true;
                break;
            }
        }
        if ($hasData) {
            return false; // Found at least one row with actual data
        }
    }

    return true; // All rows are empty/null
}


$yearList = getYearsList_tb1();
$resultList = fetchSalecodeList_tb1($year_param);
$typeList = fetchTypeList_tb();
$sexList = getSexList_tb();
$sireList = fetchSireListAll_tb($year_param);

$sortList = ["Hip", "Horse", "Sire", "Datefoal", "Dam", "Sex", "Type", "Price", "Currency", "Salecode", "Day", "Consno", "Saletype", "Age", "Rating"];

?>
<br>


<div style= "margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">WEANLING REPORT THOROUGHBRED</h1>
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
	<option value="W">W : Weanling</option>
	<option value="Y">Y : Yearling</option>
  <script>
    $(document).ready(function() {
    $('#type').val('W'); 
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
<br>
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
        	  <div class="cell"  style="width: device-width;">
                No.
              </div>
              <div class="cell"  style="width: device-width;">
                HIP
              </div>
              <div class="cell"  style="width: device-width;">
                Horse
              </div>
              <div class="cell"  style="width: device-width;">
                Sire
              </div>
              <div class="cell"  style="width: device-width;">
                Datefoal
              </div>
              <div class="cell"  style="width: device-width;">
                Dam
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
                Total
              </div>
           </div>
          
          <?php
            setlocale(LC_MONETARY,"en_US");
            $number =0;  

            if (isAllDataNull($resultFound)) {
              echo '
              <div style="
                  padding: 12px 20px;
                  margin: 20px 0;
                  border: 1px solid #f5c2c7;
                  background-color: #f8d7da;
                  color: #842029;
                  border-radius: 6px;
                  font-family: Arial, sans-serif;
                  font-size: 14px;
                  max-width: 600px;
              ">
                  <strong>Notice:</strong> No valid data found for the selected criteria. Please try adjusting your filters.
              </div>';
              } else {
                foreach($resultFound as $row) {
                    $number = $number+1;
                    $elementCount = 0;
                    $totalPrice = floatval($row['Price']); // Assuming the price column name is 'Price'
                    $offspringTotalPrice = 0;

                    echo "<div class='row'>";
                    echo "<div class='cell'>".$number."</div>";

                    foreach($row as $elements) {
                        $elementCount =$elementCount+1;
                        if($elementCount == 8){
                            $elements = "$".number_format(floatval($elements), 0);
                        }
                        if ($elements == "1900-01-01") {
                            $elements="";
                        }
                        if ($elementCount == 14) {
                            // if ($elements != "") {
                            //     $date=date_create($elements);
                            //     $elements = date_format($date,"m/d/y");
                            // }
                        }
                        if ($elementCount == 10) {
                            // $elements= substr($elements, 0,4);
                        }
                        echo "<div class='cell'>".$elements."</div>";
                    }
                    
                    $offspring_rows = fetchOffsprings_weanling_tb($row['Dam'], $row['Salecode']);

                    $offspringTotalPrice = 0;
                    foreach ($offspring_rows as $offspring_row) {
                        foreach ($offspring_row as $element) {
                            $elementCount++;
                            if ($elementCount == 20) {
                              $element = intval($element);
                              $offspringTotalPrice += $element; // Assuming the price column is at index 18
                              $element = "$" . number_format($element, 0);
                            }
                            echo "<div class='cell'>" . $element . "</div>";
                        }
                    }


                    // Calculate the total price difference
                    $priceDifference = $offspringTotalPrice - $totalPrice;
                    
                    $cellColor = ($priceDifference < 0) ? '#FF6347' : '#32CD32';

                    // Display the price difference in the "Total" column
                    if ($offspringTotalPrice > 0) {
                      echo "<div class='cell' style='width: device-width;background-color:" . $cellColor . "'>$" . number_format($priceDifference, 0) . "</div>";
                    } else {
                      echo "";
                    }
                    
                    echo "</div>";
                }
              }
          ?>
    </div>
 </div>
</div>
<br>
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

    var link ="weanling-report.php?year="+year
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
    //alert(link);
    							
  	window.open(link,"_self");
  	if(year== "" && salecode== "" && type == "" && sex== "" && sire == "")
  	{
  		alert("Please Select Atleast One Category Filter");
  	}
}
</script>

