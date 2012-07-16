<?php include('control.php'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>123Aurora Administration</title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<link rel="stylesheet" href="../styles/default/css/style.css" type="text/css">
</head>
<body>
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center" height="90%">
  <tr bgcolor="#FFFFFF"> 
    <td class="cadretopleft" width="128"><img src="../styles/default/images/sun12880.png" width="128" height="80" alt="123Aurora"></td>
  <td class="cadretop" align="center"><b>123Aurora Administration</b><br></td>
  </tr>
  <tr valign="top"> 
    <td height="100%" COLSPAN="3"> 
      <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" height="100%">
        <tr valign="top"> 
          <td width="128" class="cadrebotleft" bgcolor="#CCCC66" height="98%"> 
            <div class="menu"> 
            </div>
          </td>
          <td class="cadrebotright" bgcolor="#d3dae2" height="98%"> 
            <table border="0" cellspacing="10" cellpadding="0" width="100%" height="100%" align="center">
              <tr valign="top"> 
                <td> <!-- #BeginEditable "mainbox" -->
<?php

if (!empty ($_POST['invt_num2'])) { $invt_num2= $_POST['invt_num2']; }  
if (!empty ($_POST['ADR2'])) { $ADR2 = $_POST['ADR2']; }
if (!empty ($_POST['LOGCOM2'])) { $LOGCOM2 = $_POST['LOGCOM2']; }  
if (!empty ($_POST['PLANT_POWER2'])) { $PLANT_POWER2 = $_POST['PLANT_POWER2']; }
if (!empty ($_POST['CORRECTFACTOR2'])) { $CORRECTFACTOR2 = $_POST['CORRECTFACTOR2']; }
if (!empty ($_POST['INITIALCOUNT2'])) { $INITIALCOUNT2 = $_POST['INITIALCOUNT2']; } else { $INITIALCOUNT2=0; }
if (!empty ($_POST['INVNAME2'])) { $INVNAME2 = $_POST['INVNAME2']; }
if (!empty ($_POST['YMAX2'])) { $YMAX2 = $_POST['YMAX2']; }
if (!empty ($_POST['YINTERVAL2'])) { $YINTERVAL2 = $_POST['YINTERVAL2']; }
if (!empty ($_POST['PRODXDAYS2'])) { $PRODXDAYS2 = $_POST['PRODXDAYS2']; }
if (!empty ($_POST['LOCATION2'])) { $LOCATION2 = $_POST['LOCATION2']; }
if (!empty ($_POST['PANELS12'])) { $PANELS12 = $_POST['PANELS12']; }
if (!empty ($_POST['ROOF_ORIENTATION12'])) { $ROOF_ORIENTATION12 = $_POST['ROOF_ORIENTATION12']; }
if (!empty ($_POST['ROOF_PICTH12'])) { $ROOF_PICTH12 = $_POST['ROOF_PICTH12']; }
if (!empty ($_POST['PANELS22'])) { $PANELS22 = $_POST['PANELS22']; }
if (!empty ($_POST['ROOF_ORIENTATION22'])) { $ROOF_ORIENTATION22 = $_POST['ROOF_ORIENTATION22']; }
if (!empty ($_POST['ROOF_PICTH22'])) { $ROOF_PICTH22 = $_POST['ROOF_PICTH22']; }
if (!empty ($_POST['EXPECTEDPROD2'])) { $EXPECTEDPROD2 = $_POST['EXPECTEDPROD2']; }
if (!empty ($_POST['EXPECTJAN2'])) { $EXPECTJAN2 = $_POST['EXPECTJAN2']; }
if (!empty ($_POST['EXPECTFEB2'])) { $EXPECTFEB2 = $_POST['EXPECTFEB2']; }
if (!empty ($_POST['EXPECTMAR2'])) { $EXPECTMAR2 = $_POST['EXPECTMAR2']; }
if (!empty ($_POST['EXPECTAPR2'])) { $EXPECTAPR2 = $_POST['EXPECTAPR2']; }
if (!empty ($_POST['EXPECTMAY2'])) { $EXPECTMAY2 = $_POST['EXPECTMAY2']; }
if (!empty ($_POST['EXPECTJUN2'])) { $EXPECTJUN2 = $_POST['EXPECTJUN2']; }
if (!empty ($_POST['EXPECTJUI2'])) { $EXPECTJUI2 = $_POST['EXPECTJUI2']; }
if (!empty ($_POST['EXPECTAUG2'])) { $EXPECTAUG2 = $_POST['EXPECTAUG2']; }
if (!empty ($_POST['EXPECTSEP2'])) { $EXPECTSEP2 = $_POST['EXPECTSEP2']; }
if (!empty ($_POST['EXPECTOCT2'])) { $EXPECTOCT2 = $_POST['EXPECTOCT2']; }
if (!empty ($_POST['EXPECTNOV2'])) { $EXPECTNOV2 = $_POST['EXPECTNOV2']; }
if (!empty ($_POST['EXPECTDEC2'])) { $EXPECTDEC2 = $_POST['EXPECTDEC2']; }

if (!is_numeric($CORRECTFACTOR2)) {
echo "CORRECTFACTOR value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTJAN2)) {
echo "EXPECTJAN value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTFEB2)) {
echo "EXPECTFEB value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTMAR2)) {
echo "EXPECTMAR value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTAPR2)) {
echo "EXPECTAPR value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTMAY2)) {
echo "EXPECTMAY value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTJUN2)) {
echo "EXPECTJUN value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTJUI2)) {
echo "EXPECTJUI value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTAUG2)) {
echo "EXPECTAUG value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTSEP2)) {
echo "EXPECTSEP value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTOCT2)) {
echo "EXPECTOCT value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTNOV2)) {
echo "EXPECTNOV value not correct<br>";
$Err=true;
}
if (!is_numeric($EXPECTDEC2)) {
echo "EXPECTDEC value not correct<br>";
$Err=true;
}
 
if ($Err!=true) {
  $myFile = 'config_invt'.$invt_num2.'.php';
  $fh = fopen($myFile, 'w+') or die("<font color='#8B0000'><b>Can't open $myFile file. Configuration not saved !</b></font>");
  $stringData="<?php
if(!defined('checkaccess')){die('Direct access not permitted');}

// ### GENERAL FOR INVERTER #$invt_num2
\$ADR='$ADR2';
\$LOGCOM=$LOGCOM2;
\$PLANT_POWER='$PLANT_POWER2';
\$CORRECTFACTOR='$CORRECTFACTOR2';
\$INITIALCOUNT='$INITIALCOUNT2';
\$INVNAME='$INVNAME2';

// ### FRONT PAGE
\$YMAX='$YMAX2';
\$YINTERVAL='$YINTERVAL2';
\$PRODXDAYS='$PRODXDAYS2';

// ### INFO DETAILS
\$LOCATION='$LOCATION2';
\$PANELS1='$PANELS12';
\$ROOF_ORIENTATION1='$ROOF_ORIENTATION12';
\$ROOF_PICTH1='$ROOF_PICTH12';
\$PANELS2='$PANELS22';
\$ROOF_ORIENTATION2='$ROOF_ORIENTATION22';
\$ROOF_PICTH2='$ROOF_PICTH22';

// ### EXPECTED PRODUCTION
\$EXPECTEDPROD='$EXPECTEDPROD2';
\$EXPECTJAN='$EXPECTJAN2';
\$EXPECTFEB='$EXPECTFEB2';
\$EXPECTMAR='$EXPECTMAR2';
\$EXPECTAPR='$EXPECTAPR2';
\$EXPECTMAY='$EXPECTMAY2';
\$EXPECTJUN='$EXPECTJUN2';
\$EXPECTJUI='$EXPECTJUI2';
\$EXPECTAUG='$EXPECTAUG2';
\$EXPECTSEP='$EXPECTSEP2';
\$EXPECTOCT='$EXPECTOCT2';
\$EXPECTNOV='$EXPECTNOV2';
\$EXPECTDEC='$EXPECTDEC2';
?>
";
  fwrite($fh, $stringData);
  fclose($fh);

if (!file_exists('../data/invt'.$invt_num2.'/')) {mkdir("../data/invt$invt_num2");}
if (!file_exists('../data/invt'.$invt_num2.'/csv')) {mkdir("../data/invt$invt_num2/csv");}
if (!file_exists('../data/invt'.$invt_num2.'/infos')) {mkdir("../data/invt$invt_num2/infos");}
if (!file_exists('../data/invt'.$invt_num2.'/production')) {mkdir("../data/invt$invt_num2/production");}
if (!file_exists('../data/invt'.$invt_num2.'/errors')) {mkdir("../data/invt$invt_num2/errors");}

echo "
<br><div align=center><font color='#228B22'><b>Configuration for inverter #$invt_num2 updated</b></font>
<br>&nbsp;
<br>&nbsp;
<INPUT TYPE='button' onClick=\"location.href='admin.php'\" value='Back'>
</div>
";

} else {
echo "
<br><div align=center><font color='#8B0000'><b>Error configuration not saved !</b></font><br>
<INPUT type='button' value='Back' onclick='history.back()'>
</div>
";
}



?>
          <!-- #EndEditable -->
          
          </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>  
</body>
</html>
