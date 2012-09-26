<?php
require_once("classes/classloader.php");

$template = "green";

include("template/" . $template . "/header.php");

$content = '
Dashboard (begin pagina):
- kWh van vandaag
- kWh deze maand
- kWh totaal
- CO2 totaal
- Huidige wattage
- Grafiek van vandaag
- Grafiek van gisteren
- Grafiek laatste xx dagen';

include("template/" . $template . "/index.php");
?>
