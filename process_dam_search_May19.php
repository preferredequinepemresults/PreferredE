<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<?php

include_once("config.php");

$searchParam = trim($_POST['dam']);
$resultFound = fetchrecords($searchParam);

$offspings = fetchOffsprings($searchParam);
$damName = getDamname($searchParam);
$damsOffspings = fetchOffsprings($damName);
echo "<br>";
?>
<link rel="stylesheet" href="assets/css/table.css">


<div class="container">
  <h2><?php echo "$searchParam";?></h2>
  <div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" href="#collapse1"><?php echo "$searchParam";?> Sold at Auction</a>
        </h4>
      </div>
      <div id="collapse1" class="panel-collapse collapse in">
      
       <table class="table">
          <tr class="row header blue">
          	  <td class="cell">
                No.
              </td>
              <td class="cell">
                Horse
              </td>
              <td class="cell">
                Sex
              </td>
              <td class="cell">
                Color
              </td>
              <td class="cell">
                Gait
              </td>
              <td class="cell">
                Type
              </td>
              <td class="cell">
                Salecode
              </td>
              <td class="cell">
                Consno
              </td>
              <td class="cell">
                Price
              </td>
              <td class="cell">
                Datefoal
              </td>
              <td class="cell">
                Lastbred
              </td>
              <td class="cell">
                Sire
              </td>
              <td class="cell">
                Dam
              </td>
          </tr>
          
          <?php
              $number =0;
              foreach($resultFound as $row) {
                  $number = $number+1;
                  echo "<tr class='row'>";
                  echo "<td class='cell'>".$number."</td>";
                  #echo $row[Price];
                  foreach($row as $elements) {
                      #if
                      echo "<td class='cell'>".$elements."</td>";
                  }echo "</tr>";
              }
          ?>
     
        </table>
      </div>
    </div>
  </div>
</div>

<div class="container">
  <div class="panel-group">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" href="#collapse2"><?php echo "$searchParam";?>'s Offspring </a>
        </h4>
      </div>
      <div id="collapse2" class="panel-collapse collapse in">
      
       <div class="table">
          <div class="row header">
          	  <div class="cell">
                No.
              </div>
              <div class="cell">
                Horse
              </div>
              <div class="cell">
                Sex
              </div>
              <div class="cell">
                Color
              </div>
              <div class="cell">
                Gait
              </div>
              <div class="cell">
                Type
              </div>
              <div class="cell">
                Salecode
              </div>
              <div class="cell">
                Consno
              </div>
              <div class="cell">
                Price
              </div>
              <div class="cell">
                Datefoal
              </div>
              <div class="cell">
                Lastbred
              </div>
              <div class="cell">
                Sire
              </div>
              <div class="cell">
                Dam
              </div>
          </div>
          
          <?php
             $number =0;
             foreach($offspings as $row) {
                  $number = $number+1;
                  echo "<div class='row'>";
                  echo "<div class='cell'>".$number."</div>";
                  foreach($row as $elements) {
                      echo "<div class='cell'>".$elements."</div>";
                  }echo "</div>";
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
      
       <table style="width: 100%">
          <tr class="row header green">
          	  <td class="cell">
                No.
              </td>
              <td class="cell">
                Horse
              </td>
              <td class="cell">
                Sex
              </td>
              <td class="cell">
                Color
              </td>
              <td class="cell">
                Gait
              </td>
              <td class="cell">
                Type
              </td>
              <td class="cell">
                Salecode
              </td>
              <td class="cell">
                Consno
              </td>
              <td class="cell">
                Price
              </td>
              <td class="cell">
                Datefoal
              </td>
              <td class="cell">
                Lastbred
              </td>
              <td class="cell">
                Sire
              </td>
              <td class="cell">
                Dam
              </td>
          </tr>
          
          <?php
          $number =0;
          foreach($damsOffspings as $row) {
              
              
              $number = $number+1;
              echo "<tr class='row'>";
              echo "<td class='cell'>".$number."</td>";
              foreach($row as $elements) {
                  
                  echo "<td class='cell'>".$elements."</td>";
              }echo "</tr>";
              $damOfDamOffspings = fetchOffsprings($row['Horse']);
   
              $sequence2 = 0;
              #2nd generation
              foreach($damOfDamOffspings as $row1) {
                  $sequence2 =$sequence2+1;
                  echo "<tr class='row'>";
                  echo "<td class='cell'>".$number.".".$sequence2."</td>";
                  foreach($row1 as $elements) {
                      echo "<td class='cell'>".$elements."</td>";
                  }echo "</tr>";
                  $damOfDamOffspings1 = fetchOffsprings($row1['Horse']);
             
                  $sequence3 = 0;
                  #3rd Generation
                  foreach($damOfDamOffspings1 as $row2) {
                      $sequence3 =$sequence3+1;
                      echo "<tr class='row'>";
                      echo "<td class='cell'>".$number.".".$sequence2.".".$sequence3."</td>";
                      foreach($row2 as $elements) {
                          echo "<td class='cell'>".$elements."</td>";
                      }echo "</tr>";
                  }
              }
              }
          ?>
     
        </table>
      </div>
    </div>
  </div>
</div>
<?php

?>
