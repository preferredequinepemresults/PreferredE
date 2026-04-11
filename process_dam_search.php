<?php

include("./header.php");
include("./session_page.php");
?>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="assets/js/script.js"></script>
</head>
<?php

include_once("config.php");
$horseName = trim($_POST['dam']);
$resultFound = fetchrecords($horseName);

$offspings = fetchOffsprings($horseName);
$damName = getDamname($horseName);

$damsOffspings = fetchOffsprings($damName);
$data = getTitleData($horseName);

$damofdamName= $data['damofdam'];
if (json_encode($resultFound)=="[]") {
    $damofdamName = getdamofdam($damName);
}
$damsofdamOffspings = fetchOffsprings($damofdamName);


if (json_encode($resultFound)=="[]") {
    $titileData= $data['sireofdam']." - ".$data['damofdam']." - ".$damofdamName;
}else
    $titileData= $data['sire']." - ".$data['dam']." - ".$data['damofdam'];
    
// $horseList = getHorseList();
// $arrayHorseList="";
// foreach ($horseList as $data){
//     $arrayHorseList = $arrayHorseList.",".$data[Horse]; // etc.
// }

//echo print_r($arrayHorseList);
echo "<br>";
?>
<style>
body {
  font-family: Arial;
}

* {
  box-sizing: border-box;
}

form.example input[type=text] {
  padding: 10px;
  font-size: 17px;
  border: 1px solid grey;
  float: left;
  width: 80%;
  background: #f1f1f1;
}

form.example button {
  float: left;
  width: 20%;
  padding: 10px;
  background: #2196F3;
  color: white;
  font-size: 17px;
  border: 1px solid grey;
  border-left: none;
  cursor: pointer;
}

form.example button:hover {
  background: #0b7dda;
}

form.example::after {
  content: "";
  clear: both;
  display: table;
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="assets/css/table.css">
<h1 style="text-align:center;color:#D98880;">HORSE SEARCH REPORT</h1>
<div class="container">
<div>
   <form style="float:right;" autocomplete="off" class="example" name="dam" action="process_dam_search.php" method="post" style="margin:auto;">
          <input type="text" name="dam" id="dam" placeholder="horse name" style="border-radius:20px;" size="40" 
          		onkeyup="this.value = this.value.toUpperCase();showHint(this.value)" required/>
          <button type="submit" style="border-radius:20px;"><i class="fa fa-search"></i></button>
          <button style="border-radius:20px;" onclick="window.print()">Print this page</button>
   </form>

  
  <br>
  <h1><?php echo "$horseName";?></h1>
  <h4><?php echo "[".$titileData."]";?></h4>
</div>

  <div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" href="#collapse1"><?php echo "$horseName";?> Sold at Auction</a>
        </h4>
      </div>
      <div id="collapse1" class="panel-collapse collapse in">
      
       <div class="table" style="width: 100%;table-layout: fixed;">
          <div class="row header blue">
        	<?php
          	include("./dam_search_table_header.php");
          	?>
          </div>
          
          <?php
              $number =0;
              $collapseID = 'collapse11';
              setlocale(LC_MONETARY,"en_US");
              foreach($resultFound as $row) {
                  $elementCount = 0;
                  $number = $number+1;
                  echo "<div class='row'>";
                  #echo "<a href='process_dam_search.php?dam=".$row[Horse]."'>";
                  echo "<div class='cell' style='color:white;background-color:4485D5'>".$number."</div>";
                  #echo "</a>";
                  #echo $row[Price];
                  foreach($row as $elements) {
                      $elementCount =$elementCount+1;
                      if($elementCount == 11){
                          $elements = "$".number_format($elements);
                      }
                      if ($elements == "0000-00-00") {
                          $elements="";
                      }
                      if ($elementCount == 9 or $elementCount == 19) {
                          if ($elements != "") {
                              $date=date_create($elements);
                              $elements = date_format($date,"m/d/y");
                          }
                      }
                      if ($elementCount == 15) {
                          $elements= substr($elements, 0,4);
                      }
                      if ($elementCount == 1) {
                          echo "<a data-toggle='collapse' href='#".$collapseID.$number."'>";
                          echo "<div class='cell' >".$elements."</div>";
                          echo "</a>";
                      }else {
                          echo "<div class='cell'>".$elements."</div>";
                      }
                      if ($elementCount==19) {
                        break;
                      }
                  }echo "</div>";
                  include("./buyer_collapse.php");
              }
          ?>
     
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" href="#collapse2"><?php echo "$horseName";?>'s Offspring </a>
        </h4>
      </div>
      <div id="collapse2" class="panel-collapse collapse in">
      
       <div class="table" style="width: 100%;table-layout: fixed;">
          <div class="row header">
         	<?php
          	include("./dam_search_table_header.php");
          	?>
          </div>
          
          <?php
             $number =0;
             $collapseID = 'collapse21';
             foreach($offspings as $row) {
                  $elementCount =0;
                  $number = $number+1;
                  echo "<div class='row'>";
                  echo "<div class='cell' style='color:white;background-color:F27467'>".$number."</div>";
                  foreach($row as $elements) {
                      $elementCount = $elementCount+1;
                      if($elementCount == 11){
                          $elements = "$".number_format($elements);
                      }
                      if ($elements == "0000-00-00") {
                          $elements="";
                      }
                      if ($elementCount == 9 or $elementCount == 19) {
                          if ($elements != "") {
                              $date=date_create($elements);
                              $elements = date_format($date,"m/d/y");
                          }
                      }
                      if ($elementCount == 15) {
                          $elements= substr($elements, 0,4);
                      }
                      if ($elementCount == 1) {
                          echo "<a data-toggle='collapse' href='#".$collapseID.$number."'>";
                          echo "<div class='cell' >".$elements."</div>";
                          echo "</a>";
                      }else {
                          echo "<div class='cell'>".$elements."</div>";
                      }
                      if ($elementCount==19) {
                          break;
                      }
                  }echo "</div>";
                  include("./buyer_collapse.php");
              }
          ?>
     
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" href="#collapse3"><?php echo "$damName";?>'s Offspring </a>
        </h4>
      </div>
      <div id="collapse3" class="panel-collapse collapse in">
      
       <div class="table" style="width: 100%;table-layout: fixed;">
          <div class="row header green">
          	<?php
          	include("./dam_search_table_header.php");
          	?>
          </div>  
          
          <?php
          $number =0;
          $lastname1 = "";
          $collapseID = 'collapse31';
          foreach($damsOffspings as $row) {
              $elementCount =0;
              $number = $number+1;
              echo "<div class='row'>";
              echo "<div class='cell' style='color:white;background-color:#145A32'>".$number."</div>";
              foreach($row as $elements) {
                  $elementCount = $elementCount+1;
                  if($elementCount == 11){
                      $elements = "$".number_format($elements);
                  }
                  if ($elements == "0000-00-00") {
                      $elements="";
                  }
                  if ($elementCount == 9 or $elementCount == 19) {
                      if ($elements != "") {
                          $date=date_create($elements);
                          $elements = date_format($date,"m/d/y");
                      }
                  }
                  if ($elementCount == 15) {
                      $elements= substr($elements, 0,4);
                  }
                  if ($elementCount == 1) {
                      echo "<a data-toggle='collapse' href='#".$collapseID.$number."'>";
                      echo "<div class='cell' >".$elements."</div>";
                      echo "</a>";
                  }else {
                      echo "<div class='cell'>".$elements."</div>";
                  }
                  if ($elementCount==19) {
                      break;
                  }
              }echo "</div>";
              include("./buyer_collapse.php");
              if ($lastname1 == $row['Horse']) {
                  continue;
              }
              $damOfDamOffspings = fetchOffsprings($row['Horse']);
              $lastname1 =$row['Horse'];
   
              $sequence2 = 0;
              $lastname2 = "";
              #2nd generation
              foreach($damOfDamOffspings as $row1) {
                  $elementCount =0;
                  $sequence2 =$sequence2+1;
                  echo "<div class='row'>";
                  echo "<div class='cell' style='color:white;background-color:#52BE80'>".$number.".".$sequence2."</div>";
                  foreach($row1 as $elements) {
                      $elementCount = $elementCount+1;
                      if($elementCount == 11){
                          $elements = "$".number_format($elements);
                      }
                      if ($elements == "0000-00-00") {
                          $elements="";
                      }
                      if ($elementCount == 9 or $elementCount == 19) {
                          if ($elements != "") {
                              $date=date_create($elements);
                              $elements = date_format($date,"m/d/y");
                          }
                      }
                      if ($elementCount == 15) {
                          $elements= substr($elements, 0,4);
                      }
                      if ($elementCount == 1) {
                          echo "<a data-toggle='collapse' href='#".$collapseID.$number.$sequence2."'>";
                          echo "<div class='cell' >".$elements."</div>";
                          echo "</a>";
                      }else {
                          echo "<div class='cell'>".$elements."</div>";
                      }
                      if ($elementCount==19) {
                          break;
                      }
                  }echo "</div>";
                  include("./buyer_collapse2.php");
                  if ($lastname2 == $row1['Horse']) {
                      continue;
                  }
                  $damOfDamOffspings1 = fetchOffsprings($row1['Horse']);
                  $lastname2 =$row1['Horse'];
                  $sequence3 = 0;
                  #3rd Generation
                  foreach($damOfDamOffspings1 as $row2) {
                      $elementCount =0;
                      $sequence3 =$sequence3+1;
                      echo "<div class='row'>";
                      echo "<div class='cell' style='background-color:#D4EFDF'>".$number.".".$sequence2.".".$sequence3."</div>";
                      foreach($row2 as $elements) {
                          $elementCount = $elementCount+1;
                          if($elementCount == 11){
                              $elements = "$".number_format($elements);
                          }
                          if ($elements == "0000-00-00") {
                              $elements="";
                          }
                          if ($elementCount == 9 or $elementCount == 19) {
                              if ($elements != "") {
                                  $date=date_create($elements);
                                  $elements = date_format($date,"m/d/y");
                              }
                          }
                          if ($elementCount == 15) {
                              $elements= substr($elements, 0,4);
                          }
                          if ($elementCount == 1) {
                              echo "<a data-toggle='collapse' href='#".$collapseID.$number.$sequence2.$sequence3."'>";
                              echo "<div class='cell' >".$elements."</div>";
                              echo "</a>";
                          }else {
                              echo "<div class='cell'>".$elements."</div>";
                          }
                          if ($elementCount==19) {
                              break;
                          }
                      }echo "</div>";
                      include("./buyer_collapse3.php");
                  }
              }
           }
          ?>
     
        </div>
      </div>
    </div>
  </div>
</div>


<div class="container">
  <div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" href="#collapse4"><?php echo "$horseName";?>'s 4-Generation Lineage</a>
        </h4>
      </div>
      <div id="collapse4" class="panel-collapse collapse in">
      
       <div class="table" style="width: 100%;table-layout: fixed;">
          <div class="row header skyblue">
          	<?php
          	include("./dam_search_table_header.php");
          	?>
          </div>
          
          <?php
          $number =0;
          $lastname1 = "";
          $collapseID = 'collapse41';
          foreach($damsofdamOffspings as $row) {
              $elementCount =0;
              $number = $number+1;
              echo "<div class='row'>";
              echo "<div class='cell' style='color:white;background-color:#1B4F72'>".$number."</div>";
              foreach($row as $elements) {
                  $elementCount = $elementCount+1;
                  if($elementCount == 11){
                      $elements = "$".number_format($elements);
                  }
                  if ($elements == "0000-00-00") {
                      $elements="";
                  }
                  if ($elementCount == 9 or $elementCount == 19) {
                      if ($elements != "") {
                          $date=date_create($elements);
                          $elements = date_format($date,"m/d/y");
                      }
                  }
                  if ($elementCount == 15) {
                      $elements= substr($elements, 0,4);
                  }
                  if ($elementCount == 1) {
                      echo "<a data-toggle='collapse' href='#".$collapseID.$number."'>";
                      echo "<div class='cell' >".$elements."</div>";
                      echo "</a>";
                  }else {
                      echo "<div class='cell'>".$elements."</div>";
                  }
                  if ($elementCount==19) {
                      break;
                  }
              }echo "</div>";
              include("./buyer_collapse.php");
              if ($lastname1 == $row['Horse']) {
                  continue;
              }
              $damOfDamOffspings = fetchOffsprings($row['Horse']);
              $lastname1 =$row['Horse'];
              
              $sequence2 = 0;
              $lastname2 = "";
              #2nd generation
              foreach($damOfDamOffspings as $row1) {
                  $elementCount =0;
                  $sequence2 =$sequence2+1;
                  echo "<div class='row'>";
                  echo "<div class='cell' style='background-color:#3498DB'>".$number.".".$sequence2."</div>";
                  foreach($row1 as $elements) {
                      $elementCount = $elementCount+1;
                      if($elementCount == 11){
                          $elements = "$".number_format($elements);
                      }
                      if ($elements == "0000-00-00") {
                          $elements="";
                      }
                      if ($elementCount == 9 or $elementCount == 19) {
                          if ($elements != "") {
                              $date=date_create($elements);
                              $elements = date_format($date,"m/d/y");
                          }
                      }
                      if ($elementCount == 15) {
                          $elements= substr($elements, 0,4);
                      }
                      if ($elementCount == 1) {
                          echo "<a data-toggle='collapse' href='#".$collapseID.$number.$sequence2."'>";
                          echo "<div class='cell' >".$elements."</div>";
                          echo "</a>";
                      }else {
                          echo "<div class='cell'>".$elements."</div>";
                      }
                      if ($elementCount==19) {
                          break;
                      }
                  }echo "</div>";
                  include("./buyer_collapse2.php");
                  if ($lastname2 == $row1['Horse']) {
                      continue;
                  }
                  $damOfDamOffspings1 = fetchOffsprings($row1['Horse']);
                  $lastname2 =$row1['Horse'];
                  $sequence3 = 0;
                  #3rd Generation
                  foreach($damOfDamOffspings1 as $row2) {
                      $elementCount =0;
                      $sequence3 =$sequence3+1;
                      echo "<div class='row'>";
                      echo "<div class='cell' style='background-color:#D6EAF8'>".$number.".".$sequence2.".".$sequence3."</div>";
                      foreach($row2 as $elements) {
                          $elementCount = $elementCount+1;
                          if($elementCount == 11){
                              $elements = "$".number_format($elements);
                          }
                          if ($elements == "0000-00-00") {
                              $elements="";
                          }
                          if ($elementCount == 9 or $elementCount == 19) {
                              if ($elements != "") {
                                  $date=date_create($elements);
                                  $elements = date_format($date,"m/d/y");
                              }
                          }
                          if ($elementCount == 15) {
                              $elements= substr($elements, 0,4);
                          }
                          if ($elementCount == 1) {
                              echo "<a data-toggle='collapse' href='#".$collapseID.$number.$sequence2.$sequence3."'>";
                              echo "<div class='cell' >".$elements."</div>";
                              echo "</a>";
                          }else {
                              echo "<div class='cell'>".$elements."</div>";
                          }
                          if ($elementCount==19) {
                              break;
                          }
                      }echo "</div>";
                      include("./buyer_collapse3.php");
                  }
              }
          }
          ?>
     
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    window.onload = function() {
        document.getElementById("dam").focus();
    }
//     document.getElementById("dam").addEventListener("keydown", function(e) {
//     alert("aaa");
//     }
//     function autoComplete(value){
    	
//     }
</script>
<script>
    if("<?php echo json_encode($resultFound);?>" == "[]" && "<?php echo json_encode($offspings);?>" == "[]")
    {
    	alert("Horse Data not Found");
    }
</script>