<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Заголовок страницы</title>
    <link rel="stylesheet" href="src/css/bootstrap.css">
</head>
<body>
<main>
    <form enctype="multipart/form-data" action="" method="post">
        <div class="form-group">
            <label>Example file input</label>
            <input type="file" class="form-control-file mb-2" name="file">
            <button type="submit">Отправить</button>
        </div>
    </form>
</main>
</body>
</html>

<?php
require('src/core.php');

$dict = new Dictionary;
$showError = false;
$errorMessage = '';
$filePath = '';

if (isset($argv[1])) {
    $filePath = $argv[1];
} else if(isset($_FILES['file']['tmp_name'])) {
    $filePath = $_FILES['file']['tmp_name'];
} else {
    echo 'Файл на обработку не передан';
    $showError = true;
}

if (!$showError && !$dict->openFile($filePath)) {
    $showError = true;
    $errorMessage = 'Ошибка открытия файла';
}

if (!$showError) {
    $dict->clearDir();
}

if (!$showError && !$dict->processFile()) {
    $showError = true;
    $errorMessage = 'Ошибка чтения файла, возможно указана неверная кодировка файла';
}

if (!$showError && !$dict->writeWordsCount()) {
    $showError = true;
    $errorMessage = 'Ошибка записи данных';
}

if (!$showError) {
    echo 'Файл успешно обработан';
} else {
    echo $errorMessage;
}
