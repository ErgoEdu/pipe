<?php
// Pipe System: to construct a sql statement base in scolumns, stable, swhere to request data from database

$body = json_decode(file_get_contents('php://input'), true);

$table = $body['table']; //table name where to do the operation
$operation_type = $body['operation_type']; //gets the operation type INSERT=1; UPDATE=2, DELETE = 3, SELECT = 4, CUSTOM=5;
$pre_columns = $body['pre_columns'];  //values pre_query, in case an INSERT, te values previous to  VALUES statement, in case of UPDATE the values after SET statement,in case SELECT the columns to select
$pre_values = $body['pre_values'];  //values post to form query, in case INSERT, the values post VALUES statement , in case of SELECT or UPDATE the WHERE values

//$sreply = $body['sreply'];  //name of columns in query to return as answer, apply only in INSERT statement, or UPDATE, generally to select id autonumeric

// include connect class


$con = mysqli_connect('inabsaccom.ipagemysql.com','user_pipe21','EduJas2006Vic');  //Connect to database Server
//$con = mysqli_connect('inabsaccom.ipagemysql.com','user_pipe21','EduJas2006Vic');

//construct the $sql query in function of $operation_type
switch ($operation_type)
{
	case 1: //INSERT operation
		$sql = "INSERT INTO " . $table . " (" . $pre_columns . ") VALUES (" . $pre_values . ");";
		break;
	case 2: //UPDATE operation
		$sql = "UPDATE " . $table  . " SET " . $pre_columns . " WHERE " . $pre_values . ";";	
		break;
	case 3: //DELETE operation
		//not allowable
		break;
	case 4: //SELECT operation
		$sql = "SELECT  " .  $pre_columns  . " FROM " . $table  . " WHERE " . $pre_values . ";";	
		break;

}

mysqli_select_db($con, 'db_pipe_local');


if ($operation_type == 1) //INSERT OPERATION IS SET
{
	mysqli_query($con, $sql); //Execute INSERT operation
	//Egonzalez Comment: $row = mysqli_insert_id($con); //returns the last inserted ID, autonumeric field
	//$json['data_pipe'][] = $row;
	//echo json_encode($json);
	$sql = "select last_insert_id() as id"; //returns the last inserted ID, autonumeric field
}



if ($operation_type == 2) //UPDATE OPERATION IS SET
{
	mysqli_query($con, $sql); //Execute INSERT operation
	//Egonzalez Comment: $row = mysqli_insert_id($con); //returns the last inserted ID, autonumeric field
	//$json['data_pipe'][] = $row;
	//echo json_encode($json);
	$sql = "select current_date"; //returns the last inserted ID, autonumeric field
}


// for select operations:
$result = mysqli_query($con, $sql);


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
  echo "&fail&: No results.";
//echo $sql;
}


mysqli_close($con);


?>

