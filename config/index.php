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
if (!file_exists('/tmp/123AURORAPASS')) {
        echo "<br><div align=center><font color='#8B0000'><b>No admin session started</b></font><br><br>
        <b>Type in your terminal : </b> "; 
        echo (dirname(dirname(__FILE__)));
        echo "/scripts/123aurora.sh admin
	<br><br>
        <INPUT TYPE='button' onClick=\"location.href='index.php'\" value='Reload'>
        </div>";
         exit();
}
?>
<br>
<table border=0 align='center'>
<tr><td align='right'>
<form action='indentification.php' method='post'>
Login: <input type='text' name='login' value=''><br>
Password: <input type='password' name='mdp' value=''>
</td><td>
<input type='submit' value='OK'>
</form>
</td>
</tr></table>
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
