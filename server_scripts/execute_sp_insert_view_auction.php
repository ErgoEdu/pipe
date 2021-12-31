<?php
// Pipe System: to construct a sql statement base in scolumns, stable, swhere to request data from database

$body = json_decode(file_get_contents('php://input'), true);

$table = $body['table']; //table name where to do the operation
$operation_type = $body['operation_type']; //gets the operation type INSERT=1; UPDATE=2, DELETE = 3, SELECT = 4, CUSTOM=5;


//PARAM VALUES
$product_id = $body['product_id']; 
$date_limit_transport = $body['date_limit_transport']; 
$comments  = $body['comments']; 
$people_id_customer = $body['people_id_customer']; 
$location_begin = $body['location_begin']; 
$location_end = $body['location_end']; 
$description = $body['description']; 
$quantity = $body['quantity']; 


$con = mysqli_connect('inabsaccom.ipagemysql.com','user_pipe21','EduJas2006Vic');  //Connect to database Server
//$con = mysqli_connect('localhost','root','');  //Connect to database Server

//construct the $sql query in function of $operation_type
mysqli_select_db($con, 'db_pipe_local');


   
    $msg = "SET @msg = 0" ;  //prepare the variable out from stored procedure
    //query to execute sp
    $sql = "CALL sp_insert_view_auction(@msg," . $product_id . "," . $date_limit_transport . "," . $comments  .  "," . $people_id_customer .  "," . 
             $location_begin . "," . $location_end . "," . $description .  "," . $quantity  . ");";	
    //execute parameter out and sp
   // echo $sql;
    if (!mysqli_query($con, $msg) || !mysqli_query($con, $sql)){
        //echo "fallo la obtencion de datos (". mysqli_errno($con) . ") " .  mysqli_error($con);
       echo "fail";
    }
    //consult the parameter out from sp and create record to out
    $sql ="SELECT @msg as id";
    if (!($result = mysqli_query($con, $sql))){
       // echo "fallo la obtencion de datos (". mysqli_errno($con) . ") " .  mysqli_error($con);
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

