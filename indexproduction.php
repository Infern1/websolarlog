<?php 
include("styles/globalheader.php");
if(!empty($_POST['invtnum'])) {
    $invtnum=$_POST['invtnum'];
} else {if ($NUMINV>1) {
    $invtnum=0;
} else {$invtnum=1;
}
}
include("config/config_invt".$invtnum.".php");
if (!empty ($_POST['whichyear'])) {
    $whichyear = $_POST['whichyear'];
} else { $whichyear= date("Y");
}
if (!empty ($_POST['compare'])) {
    $compare = $_POST['compare'];
} else { $compare="";
}

if($invtnum==0) {
    $startinv=1; $uptoinv=$NUMINV;
} else { $startinv=$invtnum; $uptoinv=$invtnum;
}
for ($invt_num=$startinv;$invt_num<=$uptoinv;$invt_num++) {  // Multi
    $config_invt="config/config_invt".$invt_num.".php";
    include("$config_invt");
    $PLANT_POWER2=$PLANT_POWER+$PLANT_POWER2;
} // multi
?>
<table width="95%" border=0 align=center cellpadding="8">
	<tr>
		<td>
			<form method="POST" action="indexproduction.php">
				<?php
				if ($invtnum==0) {
				    $dir = 'data/invt1/production/';
				} else {
				    $dir = 'data/invt'.$invtnum.'/production/';
				}
				$output = scandir($dir);
				$output = array_filter($output, "tricsv");
				sort($output);
				$xyears=count($output);

				if ($NUMINV>1) {
				    echo "<select name='invtnum' onchange='this.form.submit()'>";
				    if ($invtnum==0) {
				        echo "<option SELECTED value=0>$lgALL</option>";
				    } else {
				        echo "<option value=0>$lgALL</option>";
				    }
				    for ($i=1;$i<=$NUMINV;$i++) {
				        if ($invtnum==$i) {
				            echo "<option SELECTED value=$i>";
				        } else {
				            echo "<option value=$i>";
				        }
				        echo "$lgINVT$i</option>";
				    }
				    echo "</select> ";
				}
				echo"$lgCHOOSEDATE :
				<select name='whichyear' onchange='this.form.submit()'>";
				for ($i=($xyears-1);$i>=0;$i--){
				    $option = substr($output[$i],6,4);
				    if ($whichyear==$option) {
				        echo "<option SELECTED>";
				    } else {
				        echo "<option>";
				    }
				    echo "$option</option>";
				}
				echo "
				</select>
				";
				echo "
				$lgSHOWEXPECTED :
				";
				if ($compare=="expected") {
				    echo "<input type='checkbox' name='compare' value='expected' checked onclick='if(this) this.form.submit();'>";
				} else {
				    echo "<input type='checkbox' name='compare' value='expected' onclick='if(this) this.form.submit();'>";
				}
				?>
			</form>
		</td>
	</tr>
</table>

<script type="text/javascript">
var chart;
var PLANT_POWER='<?php echo $PLANT_POWER2;?>';
  
$(document).ready(function() {
    function setChart(name, categories, data, color) {
        chart.xAxis[0].setCategories(categories);
        chart.series[0].remove();
        chart.addSeries({
            name: name,
            data: data,
            color: color || 'white'
        });
    }
    var colors = Highcharts.getOptions().colors;
    var options = ({
        chart: {
            renderTo: 'container',
            type: 'column',
            backgroundColor: null
        },
        subtitle: {
            text: '<?php echo "$lgPRODSUBTITLE";?>'
        },
        xAxis: {
            categories: [],
            minRange: 1
        },
        yAxis: {
            title: {
                text: 'kWh'
            }
        },
        minorTickInterval: 'auto',
      plotOptions: {
        series: {minPointLength: 3},
        column: {
          cursor: 'pointer',
          point: {
            events: {
              click: function() {
                var drilldown = this.drilldown;
                if (drilldown) { 
                  this.series.chart.setTitle({text: drilldown.name }, { text : '<?php echo "$lgPRODSUBTITLE2";?>'});
                  setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color);
                } else { 
                  chart.setTitle({text: chart.name}, { text : '<?php echo "$lgPRODSUBTITLE";?>'});        
                  setChart(name, categories, data);
                }
              }
            }
          },
                dataLabels: {
                    enabled: true,
                    color: colors[0],
                    style: {
                        fontWeight: 'bold'
                    },
                    formatter: function() {
                        return this.y;
                    }
                }
            }
        },
      tooltip: {
        formatter: function() {
          var point = this.point,
              s = '<b>'+ this.x + ': '+ this.y +' kWh</b>';
          if (this.point.color=='#89A54E') {
            s += '<b><?php echo " $lgPRODTOOLTIPEXPECTED";?></b>';
          } else if (point.drilldown) {
            s += '<br><?php echo "$lgEFFICIENCY";?>: ' + (this.y/(PLANT_POWER/1000)).toFixed(2)+ ' kWh/kWp';
            s += '<br><?php echo "$lgPRODTOOLTIP";?><br>';
          } else {
            s += '<br><?php echo "$lgEFFICIENCY";?>: ' + (this.y/(PLANT_POWER/1000)).toFixed(2)+ ' kWh/kWp';
            s += '<br><?php echo "$lgPRODTOOLTIP2";?>';
          }
          return s;
        }
      },
  exporting: {
  filename: '123Aurora-chart',
  width: 1200
  },
  legend: {
  enabled: false
  },
  credits: {
  enabled: false
  },
  series: []
});
<?php
$destination="programs/programproduction.php?whichyear=$whichyear&compare=$compare";
if ($invtnum==0 || $NUMINV==1) {
$parttitle="";
} else {
$parttitle="$lgINVT$invtnum - ";
}
echo "
var invtnum = $invtnum;
$.getJSON('$destination', { invtnum: invtnum }, function(JSONResponse) {
  data = JSONResponse.data; //0-level data 
  name = JSONResponse.name; //0-level name
  categories = JSONResponse.categories; //0-level categories
  options.series.push(JSONResponse);
  options.xAxis.categories = categories;
  prod_y = JSONResponse.prod_y;
  chart = new Highcharts.Chart(options);
  chart.name= '$parttitle $lgPRODTITLE $whichyear ('+ prod_y +'kWh)';
  chart.setTitle({ text: chart.name});
});

"; 
?>
}); 
</script>

<table width="100%" border=0 align=center cellpadding="0">
	<tr>
		<td><div id="container" style="width: 95%; height: 450px"></div></td>
	</tr>
</table>
<?php include("styles/".$user_style."/footer.php"); ?>