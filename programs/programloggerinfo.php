<?php
// Credit Louviaux Jean-Marc 2012
$uptime=shell_exec('uptime');
$cpuuse=shell_exec("ps aux|awk 'NR > 0 { s +=$3 }; END {print \"cpu %\",s}' | awk '{ print $3 }'");
$diskuse=shell_exec("df -h | grep root | awk '{print $2}'");
$diskfree=shell_exec("df -h | grep root | awk '{print $4}'");

$disktotal = disk_total_space(".");
$diskfree= disk_free_space(".");
$diskuse = $disktotal - $diskfree;

$meminfo = getSystemMemInfo();

$arr= array(
		'uptime' => trim($uptime),
		'cpuuse' => getCpuInfo(),
		'memtot' => $meminfo["MemTotal"],
		'memuse' => ($meminfo["MemTotal"] - $meminfo["MemFree"]),
		'memfree' => $meminfo["MemFree"],
		'memperc' => round(($meminfo["MemTotal"] - $meminfo["MemFree"]) / $meminfo["MemTotal"] * 100),
		'diskuse' => HumanSize($diskuse),
		'diskfree' => HumanSize($diskfree),
		'disktotal' => HumanSize($disktotal),
		'diskperc' => round($diskuse / $disktotal * 100)
);

$ret= array($arr);

header("Content-type: text/json");
echo json_encode($ret);


function getSystemMemInfo()
{
	$data = explode("\n", file_get_contents("/proc/meminfo"));
	$meminfo = array();
	foreach ($data as $line) {
		if (trim($line) != "") {
			list($key, $val) = explode(":", $line);
			$val = trim(str_replace("kB", "", $val));
			$meminfo[$key] = round($val / 1024); // convert to mb
		}
	}
	return $meminfo;
}

function getCpuInfo()
{
	$data = explode(" ", file_get_contents("/proc/loadavg"));
	return $data[0] * 100;
}

function HumanSize($Bytes)
{
	$Type=array("", "kilo", "mega", "giga", "tera", "peta", "exa", "zetta", "yotta");
	$Index=0;
	while($Bytes>=1024)
	{
		$Bytes/=1024;
		$Index++;
	}
	return("".round($Bytes,2)." ".$Type[$Index]."bytes");
}
?>
