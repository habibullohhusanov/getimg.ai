<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["prompt"])) {
    $prompt = $_POST["prompt"];
    $width = $_POST["width"];
    $height = $_POST["height"];
    if (empty($width)) {
        $width = 512;
    }
    if (empty($height)) {
        $height = 512;
    }
    $apiUrl = 'https://api.getimg.ai/v1/stable-diffusion/text-to-image';
    $apiKey = "";
    $headers = [
        "accept: application/json",
        "content-type: application/json",
        "authorization: $apiKey",
    ];
    $postData = [
        'prompt' => $prompt,
        'model' => 'stable-diffusion-v1-5',
        'width' => $width,
        'height' => $height,
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die('cURL xatosi: ' . curl_error($ch));
    }
    curl_close($ch);
    $result = json_decode($response);
    $imageBase64 = $result->image;
    $imageData = base64_decode($imageBase64);
    $name = date("YMDHis");
    file_put_contents('path/' . $name . '.jpg', $imageData);
    $_SESSION['path'] = 'path/' . $name . '.jpg';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST">
        <input type="text" name="prompt">
        <input type="number" name="width" max="1024">
        <input type="number" name="height" max="1024">
        <button type="submit">Send</button>
    </form>
    <?php if (isset($_SESSION["path"])) : ?>
        <img src="<?= $_SESSION["path"] ?>" alt="">
    <?php
    endif;
    unset($_SESSION["path"]);
    ?>
</body>
</html>