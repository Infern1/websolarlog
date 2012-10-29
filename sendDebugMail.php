<?php 


require_once "/home/pi/www/WebSolarLog/trunk/classes/phpmailer/class.phpmailer.php";
require_once "/home/pi/www/WebSolarLog/trunk/classes/phpmailer/class.smtp.php";


exec("top", $output);
echo "<br>";
exec("ps -eo size,pid,user,command --sort -size | awk '{ hr=$1/1024 ; printf("%13.2f Mb ",hr) } { for ( x=4 ; x<=NF ; x++ ) { printf("%s ",$x) } print "" }'", $output);
echo "<br>";

?>