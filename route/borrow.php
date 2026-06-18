<?php

require_once "../connection.php";
require_once "../cache/redis.php";
require_once "../repo/borrowRepo.php";
require_once "../controller/borrowController.php";
require_once "../helper/responce.php";

$method = $_SERVER["REQUEST_METHOD"];

if($method == "GET"){

    if(isset($_GET["id"])){

        responce(getBorrowByIdController($_GET["id"]));
    }
    else{

    responce(getAllBorrowsController());
    }
}

elseif($method == "POST"){

    $data = json_decode(file_get_contents("php://input"),true);

    responce(addBorrowController($data));
}

elseif($method == "DELETE"){

    if(isset($_GET["id"])){

        responce(deleteBorrowController($_GET["id"]));
    }
}

elseif($method == "PUT"){

    if(isset($_GET["id"])){

        $data = json_decode(file_get_contents("php://input"),true);

        responce(updateBorrowController($_GET["id"] , $data));
    }
}