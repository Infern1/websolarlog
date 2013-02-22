<html>
<head>
<meta http-equiv="refresh" content="1">
<title>Readings of a Diehl solar inverter</title>
</head>
<body>
<?php
$data_string = '{"jsonrpc":"2.0","method":"GeteNexusData","params":[{"path":"eNEXUS_0043[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0063[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0049[s:17,t:1]","datatype":"INT16U"},{"path":"eNEXUS_0050[s:17,t:1]","datatype":"INT16U"},{"path":"eNEXUS_0065[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0051[s:17,t:1]","datatype":"INT16U"},{"path":"eNEXUS_0052","datatype":"INT16U"},{"path":"eNEXUS_0053","datatype":"INT16U"},{"path":"eNEXUS_0066[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0055[s:17,t:1]","datatype":"INT16U"},{"path":"eNEXUS_0064[s:17,t:1]","datatype":"INT32U"},{"path":"eNEXUS_0056","datatype":"INT16U"},{"path":"eNEXUS_0057","datatype":"INT16U"},{"path":"eNEXUS_0058","datatype":"INT32U"},{"path":"eNEXUS_0066[s:17,t:1,p:1]","datatype":"INT32U"},{"path":"eNEXUS_0066[s:17,t:1,p:2]","datatype":"INT32U"},{"path":"eNEXUS_0066[s:17,t:1,p:3]","datatype":"INT32U"},{"path":"eNEXUS_0064[s:17,t:1,p:1]","datatype":"INT32U"},{"path":"eNEXUS_0064[s:17,t:1,p:2]","datatype":"INT32U"},{"path":"eNEXUS_0064[s:17,t:1,p:3]","datatype":"INT32U"},{"path":"eNEXUS_0009[s:17,t:1,p:1]","datatype":"INT16U"},{"path":"eNEXUS_0009[s:17,t:1,p:2]","datatype":"INT16U"},{"path":"eNEXUS_0009[s:17,t:1,p:3]","datatype":"INT16U"}],"id":0}:';
$ch = curl_init();
echo $_REQUEST[$_GET];
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

/**
{
	"jsonrpc":	"2.0",
	"result":	[{
			"path":	"eNEXUS_0063[s:17,t:1]",
			"value":	"394"
		}, {
			"path":	"eNEXUS_0049[s:17,t:1]",
			"value":	"394"
		}, {
			"path":	"eNEXUS_0050[s:17,t:1]",
			"value":	"3537"
		}, {
			"path":	"eNEXUS_0065[s:17,t:1]",
			"value":	"139"
		}, {
			"path":	"eNEXUS_0051[s:17,t:1]",
			"value":	"139"
		}, {
			"path":	"eNEXUS_0052",
			"value":	"0"
		}, {
			"path":	"eNEXUS_0053",
			"value":	"0"
		}, {
			"path":	"eNEXUS_0066[s:17,t:1]",
			"value":	"493"
		}, {
			"path":	"eNEXUS_0055[s:17,t:1]",
			"value":	"2333"
		}, {
			"path":	"eNEXUS_0064[s:17,t:1]",
			"value":	"115"
		}, {
			"path":	"eNEXUS_0056",
			"value":	"1"
		}, {
			"path":	"eNEXUS_0057",
			"value":	"1"
		}, {
			"path":	"eNEXUS_0058",
			"value":	"2000"
		}, {
			"path":	"eNEXUS_0066[s:17,t:1,p:1]",
			"value":	"493"
		}, {
			"path":	"eNEXUS_0066[s:17,t:1,p:2]",
			"value":	"0"
		}, {
			"path":	"eNEXUS_0066[s:17,t:1,p:3]",
			"value":	"0"
		}, {
			"path":	"eNEXUS_0064[s:17,t:1,p:1]",
			"value":	"115"
		}, {
			"path":	"eNEXUS_0064[s:17,t:1,p:2]",
			"value":	"0"
		}, {
			"path":	"eNEXUS_0064[s:17,t:1,p:3]",
			"value":	"0"
		}, {
			"path":	"eNEXUS_0009[s:17,t:1,p:1]",
			"value":	"2333"
		}, {
			"path":	"eNEXUS_0009[s:17,t:1,p:2]",
			"value":	"0"
		}, {
			"path":	"eNEXUS_0009[s:17,t:1,p:3]",
			"value":	"0"
		}],
	"Id":	0
}

**/

echo "<table border='1'>";
foreach (json_decode($output) as $key => $value){
	if(is_array($value)){
		foreach ($value as $keys => $values){
			if($values->path == 'eNEXUS_0043[s:17,t:1]'){ echo "<tr><td>KWHT</td><td>".($values->value/10)."</td></tr>"; }
			if($values->path == 'eNEXUS_0063[s:17,t:1]'){ echo "<tr><td>DC current</td><td>".($values->value/1000)."A</td></tr>"; }
			if($values->path == 'eNEXUS_0049[s:17,t:1]'){ echo "<tr><td>DC current</td><td>".($values->value/1000)."A</td></tr>"; }
			if($values->path == 'eNEXUS_0050[s:17,t:1]'){ echo "<tr><td>DC voltage:</td><td>".($values->value/10)."</td></tr>"; }
			if($values->path == 'eNEXUS_0065[s:17,t:1]'){ echo "<tr><td>DC Power</td><td>".$values->value."</td></tr>"; $inputPower = $values->value;}
			if($values->path == 'eNEXUS_0051[s:17,t:1]'){ echo "<tr><td>DC avg. Power</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0052'){ echo "<tr><td>Alarm count:</td><td>".$values->value."??</td></tr>"; }
			if($values->path == 'eNEXUS_0053'){ echo "<tr><td>Warnings count:</td><td>".$values->value."??</td></tr>"; }
			if($values->path == 'eNEXUS_0066[s:17,t:1]'){ echo "<tr><td>AC Current:</td><td>".($values->value/1000)."A</td></tr>"; }
			if($values->path == 'eNEXUS_0055[s:17,t:1]'){ echo "<tr><td>AC Voltage:</td><td>".($values->value/10)."V</td></tr>"; }
			if($values->path == 'eNEXUS_0064[s:17,t:1]'){ echo "<tr><td>AC Power:</td><td>".$values->value."W</td></tr>"; }
			if($values->path == 'eNEXUS_0066[s:17,t:1,p:1]'){ echo "<tr><td>AC L1Output current:</td><td>".($values->value/1000)."A</td></tr>"; }
			if($values->path == 'eNEXUS_0066[s:17,t:1,p:2]'){ echo "<tr><td>AC L2Output current:</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0066[s:17,t:1,p:3]'){ echo "<tr><td>AC L3Output current:</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0064[s:17,t:1,p:1]'){ echo "<tr><td>AC L1Output power:</td><td>".($values->value)."W</td></tr>"; $outputPower = $values->value;}
			if($values->path == 'eNEXUS_0064[s:17,t:1,p:2]'){ echo "<tr><td>AC L2Output power:</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0064[s:17,t:1,p:3]'){ echo "<tr><td>AC L3Output power:</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0009[s:17,t:1,p:1]'){ echo "<tr><td>AC L1Output voltage:</td><td>".($values->value/10)."V</td></tr>"; }
			if($values->path == 'eNEXUS_0009[s:17,t:1,p:2]'){ echo "<tr><td>AC L2Output voltage:</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0009[s:17,t:1,p:3]'){ echo "<tr><td>AC L3Output voltage:</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0056'){ echo "<tr><td>Number of inverters:</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0057'){ echo "<tr><td>Number of active inverters:</td><td>".$values->value."</td></tr>"; }
			if($values->path == 'eNEXUS_0058'){ echo "<tr><td>Plant power capacity:</td><td>".$values->value."W</td></tr>"; }
		}
	}else{
		echo '<tr><td>'.$key.":</td><td>".print_r($value)."</td></tr>";
	}

}
echo "<tr><td>DC->AC efficiency:</td><td>".round((($outputPower/$inputPower)*100),2)."%</td></tr>";
echo "<table>";


$data_string = '{"jsonrpc":"2.0","method":"GetEventPage","params":[1,"2013-02-21 23:59:59",0,14],"id":0}';
$ch = curl_init();
echo $_REQUEST[$_GET];
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

var_dump($output);


?>





</body>
</html>
