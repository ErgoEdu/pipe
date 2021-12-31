<?php
// Pipe System: to construct a sql statement base in scolumns, stable, swhere to request data from database

$body = json_decode(file_get_contents('php://input'), true);

$table = $body['table']; //table name where to do the operation
$operation_type = $body['operation_type']; //gets the operation type INSERT=1; UPDATE=2, DELETE = 3, SELECT = 4, CUSTOM=5;

//$sreply = $body['sreply'];  //name of columns in query to return as answer, apply only in INSERT statement, or UPDATE, generally to select id autonumeric

//PARAM VALUES
$address = $body['address']; 
$lat_coord = $body['lat_coord']; 
$long_coord = $body['long_coord']; 
$postalcode = $body['postalcode']; 
$subadminarea = $body['subadminarea']; 
$sublocality = $body['sublocality']; 
$adminarea = $body['adminarea']; 

$con = mysqli_connect('inabsaccom.ipagemysql.com','user_pipe21','EduJas2006Vic');  //Connect to database Server
//$con = mysqli_connect('localhost','root','');  //Connect to database Server

//construct the $sql query in function of $operation_type
mysqli_select_db($con, 'db_pipe_local');


   
    $msg = "SET @msg = 0" ;  //prepare the variable out from stored procedure
    //query to execute sp
    $sql = "CALL sp_insert_location(@msg," . $address . "," . $lat_coord . "," . $long_coord  .  "," . $postalcode .  "," . 
             $subadminarea . "," . $sublocality . "," . $adminarea . ");";	
    //execute parameter out and sp
    //echo $sql;
    if (!mysqli_query($con, $msg) || !mysqli_query($con, $sql)){
       // echo "fallo la obtencion de datos (". mysqli_errno($con) . ") " .  mysqli_error($con);
       echo "fail";
    }
    //consult the parameter out from sp and create record to out
    $sql ="SELECT @msg as id";
    if (!($result = mysqli_query($con, $sql))){
        //echo "fallo la obtencion de datos (". mysqli_errno($con) . ") " .  mysqli_error($con);
        echo "fail";
    }




// check for access to app

if (mysqli_num_rows($result) > 0) 
{
  // user autorized
	$json = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$json['data_pipe'][] = $row;

	}
  
	echo json_encode($json);
}
else
{
  // no user authorized
  echo "fail";
//echo $sql;
}


mysqli_close($con);


?>

