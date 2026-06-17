<?php

function responce($data){

    http_response_code($data["status"] ?? 200);

    header("Content-Type: application/json");

    unset($data["status"]);

    echo json_encode($data);
}