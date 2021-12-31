<?php
/* Pipe System: to get access to Database to an user of Pipe App
    Name File: save_pipe_session.php
    Created:  29/12/2021
    Inserts data in tables users and people, to create user to access pipe
    Server test: http://edwar/pipeadmin/json_files/save_pipe_session.php
    Production: http://www.ergorackperu.com/pipeadmin/json_files/save_pipe_session.php

    */



$body = json_decode(file_get_contents('php://input'), true);

$login=$body['login'];
$pwd=$body['pwd'];
$app_type_access=$body['app_type_access'];
$people_id=$body['people_id'];
$cname=$body['cname'];
// include connect class

$con = mysqli_connect('inabsaccom.ipagemysql.com','user_pipe21','EduJas2006Vic');  //Connect to database Server
//$con = mysqli_connect('localhost','root','');  //Connect to database Server

mysqli_select_db($con, 'db_pipe_local');
//$sql="SELECT user_level, people_id, app_type_access, people.cname as cname FROM users, people  WHERE people.id = users.people_id AND status_active_type ='A'  AND login=" .
 //$login . " AND pwd=" . $password;

 $sql="SELECT login FROM  users WHERE login='" .  $login . "'"  ;

$mensaje = "&fail&:";

$result = mysqli_query($con, $sql);

// check for access to app

if (mysqli_num_rows($result) > 0) 
{
  // user exist in db 
    $mensaje = "&fail&: User Exist, choose another user."; //User exist in table Users
}
else
{
  // look for people_id in people table
  $sql = "SELECT id FROM people WHERE id = '" . $people_id . "'";
  $result = mysqli_query($con, $sql);
    // Executes query, if fails echo fail
    mysqli_query($con, $sql);
    //echo "fallo la obtencion de datos (". mysqli_errno($con) . ") " .  mysqli_error($con);
    if (mysqli_num_rows($result) > 0)  // people_id exist then create just user in table users
    {
        $mensaje = "&fail&: Id (DNI) Exist, contact the administrator."; //User exist in table Users
    }
    else
    {
        // insert new people into table people_id
        $sql = "INSERT INTO people (id,cname,juridica,phone) VALUES ('" .
        $people_id . "','" . $cname . "',0,'" . $login . "')";
        mysqli_begin_transaction($con,MYSQLI_TRANS_START_READ_WRITE);
        if (!mysqli_query($con, $sql)) //success in users data
        {
            $mensaje = "&fail&: trouble inserting id(DNI)."; // woala, everything is fine
            mysqli_rollback($con);
        }
        else 
        {
            // Hash pwd
            $pwd1 = password_hash($pwd, PASSWORD_DEFAULT);
            //insert new user into table users
            $query = "INSERT INTO users (login, pwd, people_id, app_type_access) VALUES ( ?,?,?,?)";
            if ($stmt = mysqli_prepare($con, $query)) {
                mysqli_stmt_bind_param($stmt, 'ssss', $login, $pwd1, $people_id, $app_type_access);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0)
                {
                    $mensaje = "&good&:";
                    mysqli_commit($con);
                }
                else
                {
                    $mensaje = "&fail&: Trouble inserting user.";
                    mysqli_rollback($con);
                }
               // $mensaje = "fallo la obtencion de datos (". mysqli_errno($con) . ") " .  mysqli_error($con);
                mysqli_stmt_close($stmt);
            }
        }
    }
}


echo $mensaje;
//echo $sql;


?>

