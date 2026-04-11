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
  <script type="text/javascript" charset="utf8"
    src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
  <script src="assets/js/script.js"></script>
</head>
<?php
include_once("config.php");
$breed_param = $_GET['breed'] ?? '';

$resultFound = getsaledata($breed_param);

if (!empty($_POST)) {
  $salecode = trim($_POST["salecode"]);
  $breed_param = trim($_POST["breed"]);
  //$password = trim($_POST["password"]);
  //echo("alert('aaaa')");
  echo $salecode;
}
?>

<br>
<style>

  #printButton {
  position: absolute;
  right:30px;         /* Position on the right */
  padding: 16px 28px;  /* Increased padding for a larger, more clickable button */
  background-color: white; /* Blue background color */
  color: white;        /* White text color */
  border: 2px solid black;        /* Black border all around */
  border-radius: 10px;  /* Rounded corners */
  font-size: 24px;     /* Slightly larger font */
  font-weight: bold;   /* Bold text for emphasis */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow effect */
  cursor: pointer;    /* Pointer cursor on hover */
  transition: opacity 0.3s ease; /* Smooth transition for hiding/showing */
  }

  @media print {
  /* Hide Download and Delete columns when printing */
  .download-column, .delete-column, .download-header, .delete-header {
    display: none !important;
  }
  
  /* Optionally, you can also hide the "Print Page" button during printing */
  #printButton {
    display: none !important;
  }

  #submitButton {
    display: none !important;
  }

  h1 {
    display: none !important;
  }

  }

</style>

<div style="margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">
  Manage File Upload Data
</h1>
  <select style="background-color:#229954;" class="custom-select1" id="breed">
    <option value="">Breed Filter</option>
    <option value="S">S : Standardbred</option>
    <option value="T">T : Thoroughbred</option>

  </select>

  <input id="submitButton"class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit"
    style="font-size:20px; " />

  <button id="printButton" onclick="window.print()">🖨️</button>

  <hr>
  <div>
    <div class="table" style="width: device-width;">
      <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        <table id="salesTable">
          <div class="cell" style="width: device-width;">
            No.
          </div>
          <div class="cell" style="width: device-width;">
            Salecode
            <button onclick="sortTable('Salecode')">
              <img src="assets\images\sort.png" alt="Sort Salecode">
            </button>
          </div>
          <div class="cell" style="width: device-width;">
            Session
          </div>
          <div class="cell" style="width: device-width;">
            Saletype
          </div>
          <div class="cell" style="width: device-width;">
            Saledate
            <button onclick="sortTable('Saledate')">
              <img src="assets\images\sort.png" alt="Sort Saledate">
            </button>
          </div>
          <div class="cell" style="width:device-width;">
            Upload-date
            <button onclick="sortTable('upload_date')">
              <img src="assets\images\sort.png" alt="Sort Uploadtime">
            </button>
          </div>
          <div class="cell" style="width: device-width;">
            Salecount
          </div>
          <div class="cell download-header" style="width: device-width;">
            Download
          </div>
          <div class="cell delete-header" style="width: device-width;">
            Delete
          </div>
          
        </table>
      </div>

      <?php
      if(!empty($resultFound)) {
      setlocale(LC_MONETARY, "en_US");
      $number = 0;
      foreach ($resultFound as $row) {
        $number = $number + 1;
        $elementCount = 0;
        echo "<div class='row'>";
        echo "<div class='cell'>" . $number . "</div>";
        foreach ($row as $elements) {
          $elementCount = $elementCount + 1;
          if ($elements == "0000-00-00") {
            $elements = "";
          }
          if ($elementCount == 4) {
            if ($elements != "") {
              $date = date_create($elements);
              $elements = date_format($date, "m/d/y");
            }
          }
          if ($elementCount == 5) { // For upload_date column
            if ($elements != "") {
              // Assuming $elements is already a valid date string
              $date = DateTime::createFromFormat('Y-m-d H:i:s', $elements); // Adjust the format if needed
              if ($date !== false) {
                $elements = $date->format('m/d/y H:i:s'); // Format the upload_date column
              } else {
                // Handle invalid date format if necessary
                $elements = "Invalid date format";
              }
            }
          }
          echo "<div class='cell'>" . $elements . "</div>";

        }

        // Download link column
        echo "<div class='cell download-column' style='width:device-width;'>";
        echo "<a href='download_file.php?salecode=" . $row['Salecode'] . "'>";
        echo "<img src='assets/images/download-image.png' alt='Download' style='width:20px; height:20px;'/>";
        echo "</a>";
        echo "</div>";
    
        // Delete form column
        echo "<div class='cell delete-column' style='width:device-width;'>";
        echo "<form name='myform' action='" . $_SERVER['PHP_SELF'] . "' method='POST' onsubmit='return confirmDelete(\"" . $row['Salecode'] . "\")'>";
        echo "<input type='hidden' name='salecode' id='salecode' value='" . $row['Salecode'] . "' />";
        echo "<input type='hidden' name='breed' id='breed' value='" . $breed_param . "' />";
        echo "<button type='submit'>Delete</button>";
        echo "</form>";
        echo "</div>";

        echo "</div>"; // Close row div

      }}
        
        //echo "<div class='cell'><a href='javascript:deleteSaleData(`".$row[Salecode]."`);'>Delete</a></div>";
        //echo "</div>";
      

    //     ?>
  </div>
</div>
</div>
<br>
<script>
  document.getElementById('breed').value = "<?php echo $breed_param; ?>";
</script>

<script>
function sortTable(column) {
    var urlParams = new URLSearchParams(window.location.search);
    var currentSortOrder = urlParams.get('sortOrder');
    var currentOrderBy = urlParams.get('orderby');
    
    var newSortOrder;
    
    if (currentOrderBy === column && currentSortOrder === 'ASC') {
        newSortOrder = 'DESC';
    } else if (currentOrderBy === column && currentSortOrder === 'DESC') {
        newSortOrder = 'ASC';
    } else {
        // New column or first click - default to DESC since data starts as ASC
        newSortOrder = 'DESC';
    }

    window.location.href = '?orderby=' + column + '&sortOrder=' + newSortOrder + '&breed=<?php echo $breed_param; ?>';
}
</script>

<script>
function confirmDelete(salecode) {
  return confirm("You're sure you want to delete: " + salecode + "?");
}
</script>

<script>
  function getValues() {
    const breedElement = document.getElementById('breed');
    const breed = breedElement ? breedElement.value.trim() : "";

    if (!breed) {
      alert("Please select a Breed Category.");
      return false;
    }

    const link = "manage_data.php?breed=" + encodeURIComponent(breed);
    window.location.href = link;
  }
</script>


<?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salecode'])) {
    $salecode = $_POST['salecode'];
    $breed_param = $_POST['breed'];

    $result = deleteSalecode($breed_param, $salecode);

    echo "<script>alert('$result'); window.location.href='manage_data.php?breed=" . urlencode($breed_param) . "';</script>";
    exit;
  }
?>
