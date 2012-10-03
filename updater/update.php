<?php
require_once '../admin/classes/classloader.php';
$url = "svn://svn.code.sf.net/p/websolarlog/code/";

$step = Common::getValue("step",0);

$versions = array();
if ($step == 0) {
    $versions = svn_ls($url . "tags");
}

if ($step == 1) {
    $version = Common::getValue('version','',0);

    $svnurl = $url;
    if ($version == "trunk") {
        $svnurl .= "trunk";
    } else {
        $svnurl .= "tags/" . $version;
    }
    echo ($svnurl);


    prepareCheckout();
    $export_result = doCheckout($svnurl);

    if ($export_result) {
        copyToLive();

        // Cleanup
        prepareCheckout();
        echo ("Update done <br />");
        exit();
    } else {
        echo ("export failed<br />Stopped update process");
        exit();
    }
}
?>
<html>
<head>
</head>
<body>
<?php
  // ### select the version to be installed
 if ($step == 0) {
?>
<form>
<input type="hidden" name="step" value="1" />
<ul>
<?php
    foreach ($versions as $version) {
        $name = $version['name'];
        echo('<li><input type="checkbox" name="version[]" value="'.$name.'"/>'.$name.'</li>');
    }
?>
    <li><input type="checkbox" name="version[]" value="trunk">trunk - development release</li>
</ul>
<button type="submit">Updaten</button>
</form>
<?php
 } // en off version selection
?>
<?php
function prepareCheckout() {
    // We need to have an temp folder if it exist we need to remove it
    if (is_dir("temp/export")) {
        ob_flush();
        Common::rrmdir("temp/export");
    }

    // Try to create the temp folder
    Common::checkPath("temp/export");
}

function doCheckout($urlsvn) {
    // Try to do an export
    return svn_export ($urlsvn , "temp/export", false );
}

function copyToLive() {
    // We dont want to copy everything, so specify which dirs we dont want
    $skipDirs = Array( "data", "database", "updater", "scripts" );
    $source = "temp/export/";
    $target = "../";

    foreach (scandir($source) as $file) {
        // Skip files we can read and the dot(dot) folders
        if (!is_readable($source.'/'.$file) || $file == '.' || $file == '..') continue;
        if (is_dir($source.$file) && !in_array($file, $skipDirs) ) {
            // Remove the target dir before updating it
            Common::rrmdir($target . $file);

            // Make sure the target dir is available, always create it
            Common::checkPath($target . $file);

            // Copy all files over
            Common::xcopy($source . $file, $target . $file);
        }
        if (is_file($source.$file))  {
            copy($source . $file, $target . $file);
        }
    }

    // We skipped the update folder, but we want to update the update script
    copy($source . "updater/update.php", $target . "updater/update.php");

}
?>
</body>
</html>