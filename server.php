<?php
define('checkaccess', TRUE);
include("config/config_main.php");

// Retrieve action params
$method = $_GET['method'];

// Detect en load the current user_lang
if (!empty ($_POST['user_lang'])) {
    setcookie('user_lang',$_POST['user_lang'],strtotime('+5 year'));
    $user_lang=$_POST['user_lang'];
} elseif (isset($_COOKIE['user_lang'])){
    $user_lang=$_COOKIE['user_lang'];
} else {
    $user_lang="English";
}
include("languages/".$user_lang.".php");

// Detect the current user style
if (!empty ($_POST['user_style'])) {
    setcookie('user_style',$_POST['user_style'],strtotime('+5 year'));
    $user_style=$_POST['user_style'];
} elseif (isset($_COOKIE['user_style'])){
    $user_style=$_COOKIE['user_style'];
} else {
    $user_style="default";
}

// Set headers for JSON response
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

// Initialize return array
$data = array();

switch ($method) {
    case 'getLanguages':
        $languages = array();
        if ($handle = opendir('languages')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." ) {
                    $languages[] = str_replace(".php", "", $entry);
                }
            }
        }
        $data['languages'] = $languages;
        $data['currentlanguage'] = $user_lang;
        break;
    case 'getMenu':
        // TODO :: Move to json file or something???
        $menu = array();
        $menu[] = array( url => "index.php", title => $lgMINDEX);
        $menu[] = array( url => "indexdetailed.php", title => $lgMDETAILED);
        $menu[] = array( url => "indexproduction.php", title => $lgMPRODUCTION);
        $menu[] = array( url => "indexcomparison.php", title => $lgMCOMPARISON);
        $menu[] = array( url => "indexinfo.php", title => $lgMINFO);

        $data['menu'] = $menu;
    default:
        break;
}

try {
    echo json_encode($data);
} catch (Exception $e) {
    echo "error: <br/>" . $e->getMessage() ;
}
?>