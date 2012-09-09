<?php 
include("styles/globalheader.php");
include("config/config_invt".$invtnum.".php");

echo "
<table width='95%' border=0 align=center cellpadding=0 CELLSPACING=20>
<tr><td colspan='2'>
<img src='images/brightness.png' width='16' height='16' border='0'><b>&nbsp;$LOCATION ($PLANT_POWER W)</b><br>
<hr size=1>
</td></tr>
<tr><td>
<u>$lgARRAY1DETAIL</u><br>
$lgPANELS: $PANELS1<br>
$lgORIENTATION: $ROOF_ORIENTATION1&deg;, $lgPITCH: $ROOF_PICTH1&deg;
</td><td>
<u>$lgARRAY2DETAIL</u><br>
$lgPANELS: $PANELS2<br>
$lgORIENTATION: $ROOF_ORIENTATION2&deg;, $lgPITCH: $ROOF_PICTH2&deg;
</td></tr>
<tr align=center><td colspan='2'><br>
<INPUT type='button' value='$lgBACK' OnClick=\"window.location.href='indexinfo.php'\">
<hr size=1>
";

$imagetypes = array("image/jpeg", "image/gif", "image/png");

function getImages($dir)
{
	global $imagetypes;

	$retval = array();
	if(substr($dir, -1) != "/") $dir .= "/";
	//$fulldir = "{$_SERVER['DOCUMENT_ROOT']}/$dir";
	$fulldir = "$dir";

	$d = @dir($fulldir) or die("getImages: Failed opening directory $dir for reading");
	while(false !== ($entry = $d->read())) {
		if($entry[0] == ".") continue;
		$f = escapeshellarg("$fulldir$entry");
		$mimetype = trim(`file -bi $f`);
		foreach($imagetypes as $valid_type) {
			if(preg_match("@^{$valid_type}@", $mimetype)) {
				$retval[] = array(
						'file' => "$dir$entry",
						'size' => getimagesize("$fulldir$entry")
				);
				break;
			}
		}
	}
	$d->close();
	return $retval;
}

$images = getImages("images/installation");

foreach($images as $img) {
	echo "<div class='photo'>";
	echo "<img src='{$img['file']}' {$img['size'][3]} alt=''><br>\n";
	echo "<a href='{$img['file']}'>",basename($img['file']),"</a><br>\n";
	echo "</div>\n";
}
echo "
</td></tr>
</table>";

include("styles/".$user_style."/footer.php"); ?>
