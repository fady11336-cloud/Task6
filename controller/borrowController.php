<?php

function getAllBorrowsController(){

    $data = getAllBorrows();

    return[
    "status" => 200,
    "data" => $data
    ];
}

function getBorrowByIdController($id){

    $data = getBorrowById($id);

    if(!$data){
        return[
        "status" => 404,
        "message" => "borrow not found"
        ];
    }

    return[
    "status" => 200,
    "data" => $data
    ];
}

function addBorrowController($data){

    if(empty($data["user_id"]) || empty($data["book_id"]) || empty($data["date"]) || 
    empty($data["return_date"]) || empty($data["status"]))
    {
        return[
        "status" => 400,
        "message" => "fields are required"
        ];
    }

    if($data["status"] != "returned" && $data["status"] != "unreturned"){

        return[
        "status" => 400,
        "message" => "status must be returned or unreturned"
        ];
    }

    if($data["return_date"] < $data["date"]){

        return[
        "status" => 400,
        "message" => "return date should be greater than borrowing date"
        ];
    }


    //CHECK IF THE BOOK EXISTS

    $checkBook = getBookById($data["book_id"]);

    if(!$checkBook){

        return[
        "status"=>404,
        "message"=>"book not found"
        ];
    }

    if($checkBook["available_copies"] <= 0){

        return[
        "status" => 400,
        "message" => "no available copies"
        ];
    }

    //CHECK IF THE USER EXISTS

    $checkUser = getUserById($data["user_id"]);

    if(!$checkUser){

        return[
        "status"=>404,
        "message"=>"user not found"
        ];
    }

    $res = addBorrow($data);

    if(!$res){

        return[
        "status"=>500,
        "message"=>"server error"
        ];
    }

    return[
    "status" => 201,
    "message" => "added succefully"
    ];
}

function deleteBorrowController($id){

    if(!is_numeric($id)){

        return [
            "status"=>400,
            "message" => "invalid id"
            ];
    }
    // CHECK IF THE BORROW EXISTS
    $borrow = getBorrowById($id);

    if(!$borrow){

        return[
        "status"=>404,
        "message"=>"borrow not found"
        ];
    }

    $book = getBookById($id);

    $res = deleteBorrow($id,$book);

    if(!$res){
        return[
        "status"=>500,
        "message"=>"server error"
        ];
    }

    return[
    "status"=>200,
    "message"=>"deleted succefully"
    ];
}