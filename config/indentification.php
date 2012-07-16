<?php
session_start();
include("cfg.php");

if($_POST && !empty($_POST['login']) && !empty($_POST['mdp']))
{
     $password_md5 = md5($_POST['mdp'].$salt);

    if(($_admin_login == $_POST['login']) && ($password_md5 == $_admin_pass))
    {
        $_SESSION['_login'] = $_admin_login;
        $_SESSION['_pass'] = $password_md5;
        header('Location: admin.php'); 
    }
    else
    {
       $err=1; 
    }
} else {
$err=2;
}
?>
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
if ($err==1) { echo "<br><div align=center><font color='#8B0000'><b>Login or password incorrect</b></font><br><br><INPUT type='button' value='Back' onclick='history.back()'></div>";}
if ($err==2) { echo "<br><div align=center><font color='#8B0000'><b>Enter a login and a password</b></font><br><br><INPUT type='button' value='Back' onclick='history.back()'></div>";}
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

