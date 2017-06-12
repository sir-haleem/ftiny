<?php
defined('VERSION') or exit('Sorry, you are not allowed to do that!');

/*
This is a basic database connection script using DHTMLSQL
*/
require __DIR__."/dhtmlsql.php";

$dhtmlsql=new DHTMLSQL();

if(APP_MODE == 'DEV') {
    $host="localhost";
    $user="root";
    $pass="";
    $dbname="hotelshub_burial";
} else {
    $host="localhost";
    $user="hotelshu_hubuser";
    $pass="lagos@1234";
    $dbname="hotelshu_hub_burial";
}


// Connection data (server_address, database, name, poassword)
$db=DHTMLSQL::connect($host,$user,$pass,$dbname);

//confirm database connection
if(!$db->connected()) {
    exit("Unable to connect to database. Reason: ".$db->connect_error());
}

//Sets MySQL character set and collation
$db->set_charset('utf8','utf8_general_ci');
