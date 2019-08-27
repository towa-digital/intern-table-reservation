<?php

require_once("options.php");

function storeRoomDimensions($roomKey, $width, $depth) {
    if($width <= 0 || $depth <= 0) {
        return "Die Abmessungen müssen größer als 0 sein!";
    } 

    storeImpl("width_$roomKey", $width);
    storeImpl("depth_$roomKey", $depth);

    return null;
}

function getWidth($roomKey) {
    $r = getImpl("width_$roomKey");
    return ($r === null) ? 15 : $r;
}

function getDepth($roomKey) {
    $r = getImpl("depth_$roomKey");
    return ($r === null) ? 10 : $r;
}