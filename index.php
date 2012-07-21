<?php
define('checkaccess', TRUE);
include("config/config_main.php");
include_once("classes/Formulas.php");
if ($NUMINV==1) {
    header("Location: index_mono.php");
} else {
    header("Location: index_multi.php");
}
?>

