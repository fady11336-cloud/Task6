<?php

function getBookById(){

    global $connection;

    $query = $connection -> prepare("SELECT * FROM books");

    $query -> execute();

    $data = $query -> fetchAll(PDO::FETCH_ASSOC);

    return $data;
}

function getUserById(){

    global $connection;

    $query = $connection -> prepare("SELECT * FROM users");

    $query -> execute();

    $data = $query -> fetchAll(PDO::FETCH_ASSOC);

    return $data;
}

function getAllBorrows(){

    global $connection;

    $query = $connection -> prepare("SELECT * FROM borrow");
    
    $query -> execute();

    $data = $query -> fetchAll(PDO::FETCH_ASSOC);

    return $data;
}

function getBorrowById($id){

    global $connection;

    $query = $connection -> prepare("SELECT * FROM borrow WHERE id = ?");

    $query -> execute([$id]);

    $data = $query -> fetch(PDO::FETCH_ASSOC);

    return $data;
}

function addBorrow($data){

    global $connection;

    try{

    $connection -> beginTransaction();

    $add = $connection -> prepare("INSERT INTO borrow (user_id,book_id,date,return_date,status)
    VALUES(?,?,?,?,?)");

    $add -> execute([$data["user_id"],$data["book_id"],
    $data["date"],$data["return_date"],$data["status"]]);

    $updateBook = $connection -> prepare("UPDATE books SET available_copies = available_copies - 1 
    WHERE book_id = ?");

    $updateBook -> execute([$data["book_id"]]);

    $connection -> commit();

    return True;

    }catch(PDOException $e){

        $connection -> rollBack();
        return False;
    }
}

function deleteBorrow($id,$borrow){

    global $connection;

    try{

    $connection -> beginTransaction();

    $updateBook = $connection -> prepare("UPDATE books SET available_copies = available_copies + 1 
    WHERE id = ?");

    $updateBook -> execute([$borrow["book_id"]]);

    $delete = $connection -> prepare("DELETE FROM borrow WHERE id = ?");

    $delete -> execute([$id]);

    $connection -> commit();

    return true;

    }catch(PDOException $e){

        $connection -> rollBack();
        return false;
    }
}
