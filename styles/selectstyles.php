<?php // Styles selection
$currentFile = $_SERVER["PHP_SELF"];
$parts = Explode('/', $currentFile);
$currentFile=$parts[count($parts) - 1];

$cmd='ls -d styles/*/';
$output = shell_exec($cmd);
$output = explode ("\n",$output);
$xstyle = count ($output);

echo"<form method=\"POST\" action=\"$currentFile\">
<font size=\"-2\">Style :</font>
<select name='user_style' onchange='this.form.submit()' style=\"font-size:0.6em\">";
for ($i=1;$i<$xstyle;$i++){
	$option = substr_replace($output[$i-1],"",0,7);
	$option = substr_replace($option,"",-1);
	if ($user_style==$option) {
		echo "<option SELECTED>";
	} else {
		echo "<option>";
	}
	echo "$option</option>";
}
echo"</select></form>";
?>
