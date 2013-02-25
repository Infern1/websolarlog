<html>
<head>
<!--  <meta http-equiv="refresh" content="3">-->
<title>Readings of a Diehl solar inverter</title>
</head>
<body>
<?php
$data_string = '{"jsonrpc":"2.0","method":"GeteNexusData","params":[
{"path":"eNEXUS_0065[s:17,t:1]","datatype":"INT32U"},
{"path":"eNEXUS_0006[s:1,t:17]","datatype":"INT16U"},
{"path":"eNEXUS_0007[s:1,t:17]","datatype":"INT16U"},
{"path":"eNEXUS_0005[s:1,t:17]","datatype":"INT16U"},
{"path":"eNEXUS_0063[s:17,t:1]","datatype":"INT32U"},

{"path":"eNEXUS_0008[s:1,t:17]","datatype":"INT16U"},
{"path":"eNEXUS_0009[s:1,t:17]","datatype":"INT16U"},
{"path":"eNEXUS_0064[s:17,t:1]","datatype":"INT32U"},
{"path":"eNEXUS_0011[s:1,t:17]","datatype":"INT32U"},
{"path":"eNEXUS_0066[s:17,t:1]","datatype":"INT32U"},
{"path":"eNEXUS_0043[s:1,t:17,n:4]","datatype":"INT32U"},
{"path":"eNEXUS_0043[s:17,t:1,n:4]","datatype":"INT32U"},
{"path":"eNEXUS_0066[s:17,t:1,p:1]","datatype":"INT32U"},
{"path":"eNEXUS_0066[s:17,t:1,p:2]","datatype":"INT32U"},
{"path":"eNEXUS_0066[s:17,t:1,p:3]","datatype":"INT32U"},
{"path":"eNEXUS_0010[s:1,t:17]","datatype":"INT16U"},
{"path":"eNEXUS_0064[s:17,t:1,p:1]","datatype":"INT32U"},
{"path":"eNEXUS_0064[s:17,t:1,p:2]","datatype":"INT32U"},
{"path":"eNEXUS_0064[s:17,t:1,p:3]","datatype":"INT32U"},
{"path":"eNEXUS_0009[s:17,t:1,p:1]","datatype":"INT16U"},
{"path":"eNEXUS_0009[s:17,t:1,p:2]","datatype":"INT16U"},
{"path":"eNEXUS_0009[s:17,t:1,p:3]","datatype":"INT16U"},
{"path":"eNEXUS_0082[s:1,t:17]","datatype":"INT16U"}
],"id":0}:';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $_GET['a']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/diehl.txt');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);
$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

$InPower1='eNEXUS_0065[s:17,t:1]'; //	InPower
$InVoltage='eNEXUS_0006[s:1,t:17]'; //	InVoltage
$InPower2='eNEXUS_0007[s:1,t:17]'; //	InPower
$InCurrent='eNEXUS_0005[s:1,t:17]'; //	Current
$Current2='eNEXUS_0063[s:17,t:1]'; //	Current
$OutputCurrent1='eNEXUS_0008[s:1,t:17]'; //	OutCurrent
$OutVoltage='eNEXUS_0009[s:1,t:17]'; //	OutVoltage
$OperationHours='eNEXUS_0011[s:1,t:17]'; //	OperationHours
$OutputCurrent2='eNEXUS_0066[s:17,t:1]'; //	OutCurrent
$EnergyTotal='eNEXUS_0043[s:1,t:17,n:4]'; //	EnergyTotal
$EnergyTotal2='eNEXUS_0043[s:17,t:1,n:4]'; //	EnergyTotal
$OutCurrent1='eNEXUS_0066[s:17,t:1,p:1]'; //	OutCurrent1
$OutCurrent2='eNEXUS_0066[s:17,t:1,p:2]'; //	OutCurrent2
$OutCurrent3='eNEXUS_0066[s:17,t:1,p:3]'; //	OutCurrent3
$OutPower1='eNEXUS_0010[s:1,t:17]'; //	OutPower // System output
$OutPower2='eNEXUS_0064[s:17,t:1,p:1]'; //	OutPower1 //String1
$OutPower3='eNEXUS_0064[s:17,t:1]'; //	OutPower // MPP output
$OutPower4='eNEXUS_0064[s:17,t:1,p:2]'; //	OutPower2 //string2
$OutPower5='eNEXUS_0064[s:17,t:1,p:3]'; //	OutPower3 //string3
$OutVoltage1='eNEXUS_0009[s:17,t:1,p:1]'; //	OutVoltage1
$OutVoltage2='eNEXUS_0009[s:17,t:1,p:2]'; //	OutVoltage2
$OutVoltage3='eNEXUS_0009[s:17,t:1,p:3]'; //	OutVoltage3

$OutPowerReactive='eNEXUS_0082[s:1,t:17]'; //	OutPowerReactive


echo "<table border='1'>";
foreach (json_decode($output) as $key => $value){
	if(is_array($value)){
		foreach ($value as $keys => $values){
	
			if($InPower1==$values->path){
				echo '<tr><td>$InPower1</td><td>('.$values->path.')</td><td>'.$values->value.'</td></tr>';
			}
			/*
			 * 
			 */
			if($InVoltage==$values->path){
				$varInVoltage0006 = ($values->value);
				echo '<tr><td>$InVoltage</td><td>('.$values->path.')0006:</td><td>'.($values->value/10).'V</td></tr>';
			}
			if($InPower2==$values->path){
				$varInPower0007 = $values->value;
				echo '<tr><td>$InPower2</td><td>('.$values->path.')0007:</td><td>'.$values->value.'</td></tr>';
			}
			if($InCurrent==$values->path){
				$varInCurrent0005 = ($values->value);
				echo '<tr><td>$InCurrent</td><td>('.$values->path.') 0005:</td><td>'.($values->value/1000).'</td></tr>';
			}
			/*
			 * 
			 */
			if($Current2==$values->path){
				echo '<tr><td>$Current2</td><td>('.$values->path.'):</td><td>'.($values->value/1000).'</td></tr>';
			}
			if($Power==$values->path){
				echo '<tr><td>$Power</td><td>('.$values->path.'):</td><td>'.$values->value.'</td></tr>';
			}
			if($OperationHours==$values->path){
				echo '<tr><td>$OperationHours</td><td>('.$values->path.'):</td><td>'.$values->value.'('.round($values->value/3600,2).' hours)</td></tr>';
			}
			if($OutputCurrent1==$values->path){
				$OutputCurrent0008 = ($values->value);
				echo '<tr><td>$OutputCurrent1</td><td>('.$values->path.')0008:</td><td>'.($values->value/1000).'</td></tr>';
			}
			if($OutVoltage==$values->path){
				$OutVoltage0009 = ($values->value);
				echo '<tr><td>$OutVoltage</td><td>('.$values->path.')0009:</td><td>'.($values->value/10).'</td></tr>';
			}
			if($OutputCurrent2==$values->path){
				echo '<tr><td>$OutputCurrent2</td><td>('.$values->path.'):</td><td>'.($values->value/1000).'</td></tr>';
			}
			if($EnergyTotal==$values->path){
				echo '<tr><td>$EnergyTotal</td><td>('.$values->path.'):</td><td>'.$values->value.'</td></tr>';
			}
			if($EnergyTotal2==$values->path){
				echo '<tr><td>$EnergyTotal2</td><td>('.$values->path.'):</td><td>'.$values->value.'</td></tr>';
			}
			if($OutCurrent1==$values->path){
				echo '<tr><td>$OutCurrent1</td><td>('.$values->path.'):</td><td>'.($values->value/1000).'</td></tr>';
			}
			if($OutCurrent2==$values->path){
				echo '<tr><td>$OutCurrent2</td><td>('.$values->path.'):</td><td>'.($values->value/1000).'</td></tr>';
			}
			if($OutCurrent3==$values->path){
				echo '<tr><td>$OutCurrent3</td><td>('.$values->path.'):</td><td>'.($values->value/1000).'</td></tr>';
			}
			if($OutPower1==$values->path){
				$OutPower0010 = ($values->value);
				echo '<tr><td>$OutPower1</td><td>('.$values->path.')0010:</td><td>'.$values->value.'</td></tr>';
			}
			if($OutPower2==$values->path){
				echo '<tr><td>$OutPower2</td><td>('.$values->path.'):</td><td>'.$values->value.'</td></tr>';
			}
			if($OutPower3==$values->path){
				echo '<tr><td>$OutPower3</td><td>('.$values->path.'):</td><td>'.$values->value.'</td></tr>';
			}
			if($OutPower4==$values->path){
				echo '<tr><td>$OutPower4</td><td>('.$values->path.'):</td><td>'.$values->value.'</td></tr>';
			}
			if($OutPower5==$values->path){
				echo '<tr><td>$OutPower5</td><td>('.$values->path.'):</td><td>'.$values->value.'W</td></tr>';
			}
			if($OutVoltage1==$values->path){
				echo '<tr><td>$OutVoltage1</td><td>('.$values->path.'):</td><td>'.($values->value/10).'V</td></tr>';
			}
			if($OutVoltage2==$values->path){
				echo '<tr><td>$OutVoltage2</td><td>('.$values->path.'):</td><td>'.($values->value/10).'V</td></tr>';
			}
			if($OutVoltage3==$values->path){
				echo '<tr><td>$OutVoltage3</td><td>('.$values->path.'):</td><td>'.($values->value/10).'V</td></tr>';
			}

			if($OutPowerReactive==$values->path){
				echo '<tr><td>$OutPowerReactive</td><td>('.$values->path.'):</td><td>'.$values->value.'W</td></tr>';
			}
				//ITP	 	= $InPower2		(eNEXUS_0007[s:1,t:17]):	28
				//GP 		= $Power		(eNEXUS_0010[s:1,t:17]):	19
				//GV		= $OutVoltage	(eNEXUS_0009[s:1,t:17]):	235.3
				//ITV		= $InVoltage	(eNEXUS_0006[s:1,t:17]):	270.9V
				//GI		= $Current1	(eNEXUS_0005[s:1,t:17]):	0.106
			
		}
	}else{
		echo '<tr><td>>'.$key."</td><td>('.$values->path.'):</td><td>".print_r($value)."</td></tr>";
	}

}
//echo "<tr><td>DC->AC efficiency</td><td>('.$values->path.'):</td><td>".round((($outputPower/$inputPower)*100),2)."%</td></tr>";



echo"<tr><td colspan='2'>
		<br>
		<br>DC: $varInPower0007=(($varInVoltage0006*$varInCurrent0005)) = (".($varInVoltage0006*$varInCurrent0005).")
		<br>AC: $OutPower0010=(($OutVoltage0009*$OutputCurrent0008)) = (".($OutVoltage0009*$OutputCurrent0008).")
		<br>
		</td><td></td></tr>";
echo "<table>";
?>



</body>
</html>



