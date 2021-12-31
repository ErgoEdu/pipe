<?php
/* Pipe System: to get access to Database to an user of Pipe App
    Name File: init_pipe_session.php
    Created:  25/11/2021
    Consults if the user exist in DB, 
    Server test: http://edwar/pipeadmin/json_files/init_pipe_session.php
    Production: http://www.ergorackperu.com/pipeadmin/json_files/init_pipe_session.php

    */

$body = json_decode(file_get_contents('php://input'), true);

$login=$body['login'];
$password=$body['pwd'];

// hash password enter by user


$con = mysqli_connect('inabsaccom.ipagemysql.com','user_pipe21','EduJas2006Vic');  //Connect to database Server
//$con = mysqli_connect('localhost','root','');  //Connect to local server
mysqli_select_db($con, 'db_pipe_local'); // select Database

// Construct sql query for select password from table users
$sql="SELECT pwd FROM `users` WHERE login='" . $login . "'";

$result = mysqli_query($con, $sql);

$access = false;
$message = "Begin looking for user!";

if (mysqli_num_rows($result) > 0) 
{
	$row = mysqli_fetch_assoc($result);
	$password_hashed= $row["pwd"];
	if (password_verify($password, $password_hashed)) // wheter the password with hash coincide ,password_verify(string $password, string $hash)
	{
		//$echo " Password Valid-OK";
		$access = true;
	}
	else
	{
		echo "&fail&: Password Invalid! ";
		$access = false;
	}
    
	$sql = "SELECT user_level, people_id, app_type_access, people.cname as cname FROM users, people "  . 
	" WHERE people.id = users.people_id AND status_active_type ='A'  AND login= '" .  $login . "'";
	$result = mysqli_query($con, $sql);

	// check for access to app

	if (mysqli_num_rows($result) > 0 &&  $access) 
	{
	// user autorized
		$json = array();
		$row = mysqli_fetch_assoc($result);
		$json['data_pipe'][] = $row;
		echo json_encode($json);
		// execute store procedure to update auctions to any user
		$sql = "CALL sp_look_bid_winners();";	// theorically must to be run every day in an event in mysql,but we no have access to events
		mysqli_query($con, $sql);

	}
	else
	{
		// no user authorized
		echo "&fail&: User no authorized";
	}
}
else{
	echo "&fail&: No user in the database for user = " . $login;
}
/*$sql="SELECT user_level, people_id, app_type_access, people.cname as cname FROM users, people  WHERE people.id = users.people_id AND status_active_type ='A'  AND login=" .
 $login . " AND pwd = '" . $pwd . "'";
*/


//echo $message;
//echo $sql;


?>

