<?php

function getBookById($id){

    global $connection;

    $query = $connection -> prepare("SELECT * FROM books WHERE id = ?");

    $query -> execute([$id]);

    $data = $query -> fetch(PDO::FETCH_ASSOC);

    return $data;
}

function getUserById($id){

    global $connection;

    $query = $connection -> prepare("SELECT * FROM user WHERE id = ?");
 
    $query -> execute([$id]);

    $data = $query -> fetch(PDO::FETCH_ASSOC);

    return $data;
}

function getAllBorrows(){

    global $connection;
    

    $cacheKey = "borrow:all";

    if(redis() -> exists($cacheKey)){

        return json_decode(
        redis() -> get($cacheKey),
        true
        );
    }

    $query = $connection -> prepare("SELECT * FROM borrow");
    
    $query -> execute();

    $data = $query -> fetchAll(PDO::FETCH_ASSOC);

    redis() -> setex(
        $cacheKey,
        600,
        json_encode($data)
    );

    return $data;
}

function getBorrowById($id){

    global $connection;
    

    $cacheKey = "borrow:".$id;

    if(redis() -> exists($cacheKey)){

        return json_decode(
            redis() -> get($cacheKey),
            true
        );
    }

    $query = $connection -> prepare("SELECT * FROM borrow WHERE id = ?");

    $query -> execute([$id]);

    $data = $query -> fetch(PDO::FETCH_ASSOC);

    redis() -> setex(
        $cacheKey,
        600,
        json_encode($data)
    );

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
    WHERE id = ?");

    $updateBook -> execute([$data["book_id"]]);

    $connection -> commit();

    redis()->del("borrow:all");

    return True;

    }catch(PDOException $e){

        $connection -> rollBack();
        echo $e->getMessage();
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

    redis()->del("borrow:all");
    redis()->del("borrow:".$id);

    return true;

    }catch(PDOException $e){

        $connection -> rollBack();
        return false;
    }
}
function updateBorrow($id,$data){

    global $connection;

    try{

        $query = $connection->prepare("UPDATE borrow SET user_id = ?,book_id = ?,date = ?,return_date = ?,
            status = ? WHERE id = ?");

        $query->execute([$data["user_id"],$data["book_id"],$data["date"],$data["return_date"],
            $data["status"],$id]);

        redis()->del("borrow:all");
        redis()->del("borrow:".$id);

        return true;

    }catch(PDOException $e){

        return false;
    }
}