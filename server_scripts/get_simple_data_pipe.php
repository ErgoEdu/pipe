<?php
// Pipe System: to construct a sql statement base in scolumns, stable, swhere to request data from database

$body = json_decode(file_get_contents('php://input'), true);

$scolumns = $body['scolumns'];
$stable = $body['stable'];
$swhere = $body['swhere'];

// include connect class


$con = mysqli_connect('inabsaccom.ipagemysql.com','user_pipe21','EduJas2006Vic');  //Connect to database Server
//$con = mysqli_connect('inabsaccom.ipagemysql.com','user_pipe21','EduJas2006Vic');
mysqli_select_db($con, 'db_pipe_local');
$sql = "SELECT " . $scolumns . " FROM " . $stable . " WHERE " . $swhere;

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
  echo "fail";
//echo $sql;
}


mysqli_close($con);
?>

