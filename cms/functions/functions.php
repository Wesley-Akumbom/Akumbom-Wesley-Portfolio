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

function getAboutData($conn) {
    $stmt = $conn->prepare("SELECT * FROM About ORDER BY exp_years DESC");
    $stmt->execute();
    $aboutData = $stmt->fetchAll(PDO::FETCH_OBJ);

    $experience = [];
    $education = [];

    foreach ($aboutData as $item) {
        if (!empty($item->exp_years) && !empty($item->exp_field)) {
            $experience[] = [
                'years' => $item->exp_years,
                'field' => $item->exp_field,
            ];
        }
        if (!empty($item->level) && !empty($item->certificate)) {
            $education[] = [
                'level' => $item->level,
                'certificate' => $item->certificate,
                'year' => $item->year
            ];
        }
    }

    return [
        'experience' => $experience,
        'education' => $education,

    ];
}