<?php
// functions/functions.php

function handleFileUpload($file, $targetDir) {
    $targetFile = $targetDir . basename($file["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

    // Check file size
    if ($file["size"] > 5000000) { // 5MB limit
        return false;
    }

    // Allow certain file formats
    $allowedExtensions = ["jpg", "jpeg", "png", "gif", "pdf"];
    if (!in_array($fileType, $allowedExtensions)) {
        return false;
    }

    // Upload file
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile;
    }

    return false;
}

function sanitizeInput($input, $type) {
    switch ($type) {
        case 'string':
            return filter_var($input, FILTER_SANITIZE_STRING);
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        default:
            return $input;
    }
}