<?php
$callback = $_GET['CKEditorFuncNum'];
$file_name = $_FILES['upload']['name'];
$file_name_tmp = $_FILES['upload']['tmp_name'];
$file_new_name = 'upload/images/';
$full_path = $file_new_name.$file_name;
$http_path = '/upload/images/'.$file_name;

$error = '';
if( move_uploaded_file($file_name_tmp, $full_path) ) {
} else {
    $error = 'Some error occured please try again later';
    $http_path = '';
}
echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$callback.",  \"".$http_path."\", \"".$error."\" );</script>";
?>