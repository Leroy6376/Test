<?php
$link = mysqli_connect("localhost", "root", "");

if ($link === false) {
    $handle = fopen('mysql.log', "w+");
    fwrite($handle, mysqli_connect_error());
    fclose($handle);
} else {
    foreach (scandir('library') as $dir) {
        if ($dir === '.' || $dir === '..') {
            continue;
        }
        $countHandle = fopen("library/$dir/count.txt", "r");
        $buffer = fgets($countHandle, 1024);
        $sql = 'INSERT INTO letters VALUES ($dir, $buffer)';
        $result = mysqli_query($link, $sql);
    }
    mysqli_close($link);
}


