<?php // Language selection
$currentFile = $_SERVER["PHP_SELF"];
$parts = Explode('/', $currentFile);
$currentFile=$parts[count($parts) - 1];

$cmd='ls languages/ | grep .php';
$output = shell_exec($cmd);
$output = explode ("\n",$output);
$xlang = count ($output);

echo"<form method=\"POST\" action=\"$currentFile\">
<select name='user_lang' onchange='this.form.submit()'>";
for ($i=1;$i<$xlang;$i++){ 
$option = substr_replace($output[$i-1],"",-4);
  if ($user_lang==$option) {
  echo "<option SELECTED>";
  } else {
  echo "<option>";
  }
echo "$option</option>";
}
echo "</select>&nbsp;
</form>
";
?>
