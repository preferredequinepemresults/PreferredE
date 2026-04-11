<?php
include("./header.php");
include("./session_page.php");
?>

<head>
  <meta name="viewport" content="width=device-width initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/css/table.css">

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>


</head>
<br>
<?php
include_once("config.php");

use Phppot\DataSource;

require_once 'DataSource.php';
$db = new DataSource();

$user = trim($_GET["user"]);
$fname = trim($_GET["fname"]);
$lname = trim($_GET["lname"]);
$password = trim($_GET["password"]);
$userrole = trim($_GET["userrole"]);
$contact = "";
$email = "";

//$secure_pass = password_hash($password, PASSWORD_BCRYPT);
echo '<br>';
echo '<br>';


$user_ID = getUserID($user);

if ($user_ID == "") {
    $sqlInsertDamsire = "INSERT into users
                    (USERNAME,FNAME,LNAME,CONTACT,EMAIL,PASSWORD,ACTIVE,USERROLE)
                    VALUES (?,?,?,?,?,?,?,?)";
    $paramType = "ssssssss";
    
    $paramArray1 = array(
        $user,
        $fname,
        $lname,
        $contact,
        $email,
        $password,
        'N',
        $userrole
    );
    $insertId = $db->insert($sqlInsertDamsire, $paramType, $paramArray1);
    
    if (! empty($insertId)) {
        $response = "Success";
        $message = "User Created Sucessfully : User: ".$user;
    } else {
        $response = "Error";
        $message = "Problem in creating new user";
    }
    //echo $response;
    //echo $message;
    
    echo "<h1 style='text-align:center;color:#D98880;'>".$response." :- ".$message."</h3>";
}else 
{
    echo "<h1 style='text-align:center;color:#D98880;'> User Already Exist : ".$user."</h3>";
}

?>