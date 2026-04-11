<?php
include("./header.php");
include("./session_page.php");
?>

<head>
  <meta name="viewport" content="width=device-width</h4> initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/css/table.css">
  
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>


</head>
<?php
include_once("config.php");
?>
<br>

<div style= "margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">STANDARD BRED SALES FILE UPLOAD</h1>
<hr>

<h2>Import CSV File</h2>
<div id="response"
        class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>">
        <?php if(!empty($message)) { echo $message; } ?>
        </div>
<form class="form-horizontal" action="import_csv.php" method="post"
                name="frmCSVImport" id="frmCSVImport"
                enctype="multipart/form-data">
    <table border="1">
        <tr >
        <td colspan="2" align="center"><strong>Import CSV file</strong></td>
        </tr>
        <tr>
        <td align="center">CSV File:</td><td><input type="file" name="file" id="file" accept=".csv"></td></tr>
        <tr >
        <td colspan="2" align="center"><input type="submit" value="submit" name="import"></td>
        </tr>
    </table>
</form>

<div style="text-align:center;align:right;font-size: 12px;">
<h2><a href="file_download.php?bred=S">Download Sample CSV file</a></h2>
<!-- <h2><a href="http://www.preferredequineresults.com/PreferredE/sampleFileUpload.csv">Download Sample CSV file</a></h2> -->
</div>
<div style="text-align:left;align:left;font-size: 12px;">
    	<h3>Please Upload data in below format:</h3>
    	
    	<div class="table" style="width: 15%;align:right;">
            <div class="row header green" style="line-height: 25px;font-size: 12px;border: 1px solid white;position: sticky;top: 0;">
              <div class="cell" style="width: 100%;">
                COLUMNS
              </div>
            </div>
            <div class='row'>
              <div class="cell">"SALEYEAR" int(4) </div> </div> <div class='row'>
              <div class="cell">"SALETYPE" varchar(1) </div> </div> <div class='row'>
              <div class="cell">"SALECODE" varchar(20) </div> </div> <div class='row'>
              <div class="cell">"SALEDATE" date (YYYY-MM-DD)</div> </div> <div class='row'>
              <div class="cell">"BOOK" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"DAY" tinyint </div> </div> <div class='row'>
              <div class="cell">"HIP" varchar(6) </div> </div> <div class='row'>
              <div class="cell">"HIPNUM" int </div> </div> <div class='row'>
              <div class="cell">"HORSE" varchar(35) </div> </div> <div class='row'>
              <div class="cell">"CHORSE" varchar(35) </div> </div> <div class='row'>
              <div class="cell">"RATING" varchar(5) </div> </div> <div class='row'>
              <div class="cell">"TATTOO" varchar(6) </div> </div> <div class='row'>
              <div class="cell">"DATEFOAL" date (YYYY-MM-DD) </div> </div> <div class='row'>
              <div class="cell">"AGE" int  default 0</div> </div> <div class='row'>
              <div class="cell">"COLOR" varchar(5) </div> </div> <div class='row'>
              <div class="cell">"SEX" varchar(3) </div> </div> <div class='row'>
              <div class="cell">"GAIT" varchar(3) </div> </div> <div class='row'>	
              <div class="cell">"TYPE" varchar(3) </div> </div> <div class='row'>
              <div class="cell">"RECORD" varchar(25) </div> </div> <div class='row'>
              <div class="cell">"ET" varchar(1) </div> </div> <div class='row'>
              <div class="cell">"ELIG" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"SIRE" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"CSIRE" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"DAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"CDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"SIREOFDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"CSIREOFDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"DAMOFDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"CDAMOFDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"DAMTATT" varchar(6) </div> </div> <div class='row'>
              <div class="cell">"DAMYOF" int (YYYY)</div> </div> <div class='row'>
              <div class="cell">"DDAMTATT" varchar(6)</div> </div> <div class='row'>
              <div class="cell">"BREDTO" varchar(20) </div> </div> <div class='row'>
              <div class="cell">"LASTBRED" date (YYYY-MM-DD)</div> </div> <div class='row'>
              <div class="cell">"CONSLNAME" varchar(60) </div> </div> <div class='row'>
              <div class="cell">"CONSNO" varchar(20) </div> </div> <div class='row'>
              <div class="cell">"PEMCODE" varchar(15) </div> </div> <div class='row'>
              <div class="cell">"PURFNAME" varchar(30) </div> </div> <div class='row'>
              <div class="cell">"PURLNAME" varchar(70) </div> </div> <div class='row'>
              <div class="cell">"SBCITY" varchar(25) </div> </div> <div class='row'>
              <div class="cell">"SBSTATE" varchar(10) </div> </div> <div class='row'>
              <div class="cell">"SBCOUNTRY" varchar(15) </div> </div> <div class='row'>
              <div class="cell">"PRICE" double </div> </div> <div class='row'>
              <div class="cell">"CURRENCY" varchar(3) </div> </div> <div class='row'>
              <div class="cell">"URL" varchar(150) </div> </div> <div class='row'>
              <div class="cell">"NFFM" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"PRIVATESALE" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"BREED" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"YEARFOAL" int(YYYY) </div> </div> <div class='row'>
              <div class="cell">"Sire" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"Sireofdam" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"DAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"FARMNAME" varchar(255) </div> </div> <div class='row'>
              <div class="cell">"FARMCODE" varchar(255) </div> </div> <div class='row'>
              <div class="cell">"SaleBarn" varchar(255) </div> </div> <div class='row'>
              <div class="cell">"SaleSection" varchar(255) </div> </div> <div class='row'>
              <div class="cell">"SaleStall" varchar(255) </div> </div>
        </div>
    </div>
</div>
<br>