<?php
$filename = 'app/files/samples/products-stock-sample.xlsx';

if (file_exists($filename)) {
    echo "The file $filename exists";
} else {
    echo "The file $filename does not exist";
}
?>
