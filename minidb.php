<?php

if ($_GET["function"] == "getCheck") {
    getCheck();
} elseif ($_GET["function"] == "updateCheckboxState") {
    updateCheckboxState($_GET["id"], $_GET["isChecked"]);
}

function getCheck($id) {
    $jsonString = file_get_contents('minidb.json');
    $jsonArray = json_decode($jsonString, true);
    return $jsonArray[$id];
}

function updateCheckboxState($id, $isChecked) {
    $jsonString = file_get_contents('minidb.json');
    $jsonArray = json_decode($jsonString, true);
    $jsonArray[$id] = $isChecked;
    $updatedJsonString = json_encode($jsonArray, JSON_PRETTY_PRINT);
    file_put_contents('minidb.json', $updatedJsonString);
}
