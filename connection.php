<?php

$localHost = "localhost";
$dbname = "library_db";
$user = "root";
$pass = "";

try{

    $connection = new PDO("mysql:host=$localHost;dbname=$dbname;",$user,$pass);
    $connection -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

}catch(PDOException $e){

    echo "connection failed".$e->getMessage();
} 