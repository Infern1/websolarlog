<?php
class GraphService {
	public static $tblGraph = "graph";
	public static $tblAxes = "graph_axes";
	public static $tblSeries = "graph_series";
	private $config;

	function __construct() {
		$this->config = Session::getConfig();
		HookHandler::getInstance()->add("onJanitorDbCheck", "GraphService.janitorDbCheck");
	}

	public static function janitorDbCheck(){
		self::installGraph();
	}

	public static function installGraph($reset=false){
		$series = array();
		HookHandler::getInstance()->fire("onDebug", "Run GraphService::janitorDbCheck->installGraph");
		$graph = R::load('graph',1);

		// 'axe' is a old table, we now use 'axes'. So if we have this table, we need to reset the graph.
		$axe_exist = R::find('axe',1);

		$checkOldSerie = (R::count('graph_series',' name = :name',array(':name'=>'cumGasL'))>0) ? true : false;
		
		HookHandler::getInstance()->fire("onDebug", 
		"graph->json:".(($graph->json) ? 'true' : 'false')." \r\n ".
		"graph:".(($graph) ? 'true' : 'false')." \r\n ".
		"reset:".print_r($reset,true)." \r\n ".
		"axe_exists:".print_r($axe_exist,true)." \r\n ".
		"checkOldSerie".print_r($checkOldSerie,true));
		
		if ($graph && ($graph->json=='null' || $reset == true || $axe_exist || $checkOldSerie)){
			R::exec( "DROP TABLE IF EXISTS axes;" );
			R::exec( "DROP TABLE IF EXISTS axe;" );

			R::exec( "DROP TABLE IF EXISTS axes_graph;" );
			R::exec( "DROP TABLE IF EXISTS axe_graph;" );
			R::exec( "DROP TABLE IF EXISTS graph;" );
			R::exec( "DROP TABLE IF EXISTS graph_series;" );
			R::exec( "DROP TABLE IF EXISTS graph_serie;" );
			R::exec( "DROP TABLE IF EXISTS series;" );
			R::exec( "DROP TABLE IF EXISTS serie;" );
			HookHandler::getInstance()->fire("onDebug",'all tabels dropped');
			$graph = null;
		}
		
		

		if (!$graph){

			$graphBean = R::dispense('graph');

			$graphHook = HookHandler::getInstance()->fire("installGraph");

			$metaData = array(
					'legend'=>array(
							'show'=>true,
							'location'=>'nw',
							'renderer'=>'EnhancedLegendRenderer',
							'rendererOptions'=>array('seriesToggle'=>'normal'),
							'width'=>0,
							'left'=>0)
			);
			if(isset($graphHook->metaData) && is_array($graphHook->metaData)){
				$metaData = array_merge($metaData,$graphHook->metaData);
			}

			$graphBean->name = 'daily';
			$graphBean->json = json_encode($metaData);

			$defaultSeries = HookHandler::getInstance()->fire("defaultSeries");
			$series = array_merge(self::defaultSeries(),$defaultSeries);
			$graphBean->sharedSeries = $series;

			$defaultAxes = HookHandler::getInstance()->fire("defaultAxes");
			$axes = array_merge(self::defaultAxes(),$defaultAxes);
			$graphBean->sharedAxes = $axes;

			R::store($graphBean);

			$graphBean = R::findOne('graph',' name = "daily" ');

			$links = $graphBean->ownGraph_series;
			$i=0;
			foreach($links as $link){
				$link->show = true;
				$link->disabled = false;
				$link->name = $series[$i]->name;
				$link->json = $series[$i]->json;
				if($i>0){
					if($order>$series[$i]->order){
						$order = $order+1;
					}else{
						$order = $series[$i]->order;
					}
				}else{
					$order = 0;
				}
				$link->order = $order;
				$i++;
			}
			R::store($graphBean);
			$links = null;
			
			$links = $graphBean->ownAxes_graph;
			$i=0;
			foreach($links as $link){
				$link->json = $axes[$i]->json;
				if($i>0){
					if($order>$axes[$i]->order){
						$order = $order+1;
					}else{
						$order = $axes[$i]->order;
					}
				}else{
					$order = 0;
				}
				$link->order = $axes[$i]->order;
				$i++;
			}
			
			R::store($graphBean);

		}
	}


	public static function defaultSeries(){
		$show = 'false';
		foreach (Session::getConfig()->devices as $device) {
			if($device->type == "production"){
				$show = 'true';
			}
		}
		$serie = R::dispense('series',2);
		$serie[0]['json'] = json_encode(array('label'=>'Cum Power (Wh)','yaxis'=>'y2axis'));
		$serie[0]['name'] = 'cumPowerWh';
		$serie[0]['show'] = $show;
		$serie[0]['disabled'] = 'false';
		$serie[0]['addon'] = 'wsl';
		$serie[0]['order'] = 0;
		$serie[1]['json'] = json_encode(array('label'=>'Avg Power (W)','yaxis'=>'yaxis'));
		$serie[1]['name'] = 'avgPowerW';
		$serie[1]['show'] = $show;
		$serie[1]['disabled'] = 'false';
		$serie[1]['addon'] = 'wsl';
		$serie[1]['order'] = 1;
		return $serie;
	}

	public static function defaultAxes(){
		$show = 'false';
		foreach (Session::getConfig()->devices as $device) {
			if($device->type == "production"){
				$show = 'true';
			}
		}
		$axe = R::dispense('axes',3);
		$axe[0]['json'] = json_encode(array('axe'=>'yaxis','label'=>'Avg Power (Wh)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer'));
		$axe[0]['show'] = $show;
		$axe[0]['order'] = 1;
		$axe[0]['addon'] = 'wsl';
		$axe[1]['json'] = json_encode(array('axe'=>'y2axis','label'=>'Cum Power (W)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer'));
		$axe[1]['show'] = $show;
		$axe[1]['order'] = 2;
		$axe[1]['addon'] = 'wsl';
		$axe[2]['json'] = json_encode(
				array('axe'=>'xaxis','label'=>'','renderer'=>'DateAxisRenderer','tickRenderer'=>'CanvasAxisTickRenderer','labelRenderer'=>'CanvasAxisLabelRenderer','tickInterval'=>3600,
						'tickOptions'=>array('formatter'=>'DayDateTickFormatter','angle'=>-45)));
		$axe[2]['show'] = $show;
		$axe[2]['order'] = 0;
		$axe[2]['addon'] = 'wsl';
		return $axe;
	}

	public  function getAllGraphs(){
		return R::find('graph');
	}

	public function getGraph($id){
		return R::find('graph',' id = :id ',array(':id'=>$id));
	}

	/**
	 *
	 * @param Graph $graph
	 * @return unknown
	 */
	public function loadGraph($options){

		$_SESSION['timers']['GraphService_BeginLoadGraph'] =(microtime(true)-$_SESSION['timerBegin'] );

		$config = Session::getConfig();
		$graph = R::findOne('graph',' name = "daily" ');
		$_SESSION['timers']['GraphService_RedBeanFindGraph'] =(microtime(true)-$_SESSION['timerBegin'] );

		// translate hideSerie labels
		$_SESSION['timers']['GraphService_BeforeGraphJSONDecde'] =(microtime(true)-$_SESSION['timerBegin'] );
		$graphJson = json_decode($graph->json);
		$_SESSION['timers']['GraphService_AfterGraphJSONDecode'] =(microtime(true)-$_SESSION['timerBegin'] );
		$i=0;



		// find series of graph
		$_SESSION['timers']['GraphService_Before_OwnGraph_series'] =(microtime(true)-$_SESSION['timerBegin'] );
		//$series = $graph->ownGraph_series;
		$_SESSION['timers']['GraphService_After_OwnGraph_series'] =(microtime(true)-$_SESSION['timerBegin'] );

		$_SESSION['timers']['GraphService_Before_exportAll_series'] =(microtime(true)-$_SESSION['timerBegin'] );
		//var_dump($ownGraphSeries);
		$series = $graph->ownGraph_series;
		$_SESSION['timers']['GraphService_After_exportAll'] =(microtime(true)-$_SESSION['timerBegin'] );

		//translate serie labels
		$seriesNew = array();
		$disabledSeries = array();
		$hideSeries = array();
		$i=0;
		$ii=0;
		foreach ($series as $serie){

			$seriesNew[$i]['name'] = $serie['name'];

			if($options['mode']=='frontend'){
				$json = json_decode($serie['json']);
				$json->label = _($json->label);

				if($serie['show']== true){
					// we want to show this serie.
					$seriesNew[$i]['id'] = $serie['id'];
					$seriesNew[$i]['json'] = json_encode($json);
					$seriesNew[$i]['disabled'] = $serie['disabled'];
					$seriesNew[$i]['order'] = $serie['order'];
					if($serie['disabled']==1){
						$hideSeries[]= $json->label;
					}
					$i++;
				}else{
					// we DON'T want to show this serie
					$disabledSeries[$ii] =$serie['name'];
					$ii++;
				}
			}else{
				$seriesNew[$i]['id'] = $serie['id'];
				$seriesNew[$i]['disabled'] = $serie['disabled'];
				$seriesNew[$i]['show'] = $serie['show'];
				$seriesNew[$i]['json'] = json_decode($serie['json']);
				$seriesNew[$i]['order'] = $serie['order'];
				$i++;
			}
				
		}
		$series = $seriesNew;
		
		usort($series, function($a, $b) {
			return $a['order'] - $b['order'];
		});
		
		// find axes of graph
		$axes = $graph->ownAxes_graph;
		$_SESSION['timers']['GraphService_sharedAxes'] =(microtime(true)-$_SESSION['timerBegin'] );
		
		usort($axes, function($a, $b) {
			return $a['order'] - $b['order'];
		});
		
		$axesList = array();
		$i=0;
		$x=0;
		$y=0;
		foreach ($axes as $axe){
			$json = json_decode($axe['json']);
			$axesList[$i]['axe'] = $json->axe;
			$axesList[$i]['label'] = $json->label;
			
			$axeNew[$i]['json'] =  $json;
			$axeNew[$i]['name'] = (strlen($json->axe)<=5) ? 'left-'.($i+1) : 'right';
			$axeNew[$i]['id'] = $axe['id'];
			if(is_int(strpos($json->axe,"y"))){
				$axeNew[$i]['axeType'] = 'y';
				$axeNew[$i]['axe'] = $json->axe;
					
				if($y==0){
					$axeNew[$i]['name'] = 'left-'.($y+1);
				}else{
					$axeNew[$i]['name'] = 'right-'.($y+1);
				}
				$y++;
			}else{
				$axeNew[$i]['axeType'] = 'x';
				$axeNew[$i]['axe'] = $json->axe;
				if($x==0){
					$axeNew[$i]['name'] = 'bottom-'.($x+1);
				}else{
					$axeNew[$i]['name'] = 'top-'.($x+1);
				}
				$x++;
			}
			$i++;
		}
		$axes = $axeNew;

		if($options['mode']=='frontend'){
			$timestamp = array("beginDate"=>strtotime($options['date']." 06:00")-3600,"endDate"=>strtotime("21:00")+3600);
				
			foreach ($config->devices as $device){
				if($device->type=='production'){
					foreach ($device->panels as $panel){
						$panels[] = array(
								'roofOrientation'=>$panel->roofOrientation,
								'roofPitch'=>$panel->roofPitch,
								'totalWp'=>$panel->amount*$panel->wp
						);
					}
					$inverters[] = array(
							'plantPower'=>$device->plantpower,
							'panels'=> (isset($panels) ? $panels : 0)
					);
				}
			}
			$slimConfig['lat'] = $config->latitude;
			$slimConfig['long']= $config->longitude;
			$slimConfig['inverters'] = $inverters;
			$util = new Util();
			$sunInfo = $util->getSunInfo($config, $options['date']);
				
				
			// get data of graph
			//$graphDataService = new GraphDataService();
				
				
			//echo "deviceNum".$options['deviceNum'];
			$_SESSION['timers']['GraphService_beforeHookDayPoints'] =(microtime(true)-$_SESSION['timerBegin'] );
			$graphHook = array();
			foreach($config->devices as $device){
				$hookReturn = HookHandler::getInstance()->fire("GraphDayPoints",$device,$options['date'],$options['type'],$disabledSeries);
				
				if(is_object($hookReturn)){
					$graphHook = array_merge_recursive((array)$graphHook,(array)$hookReturn);
				}
			}
			//var_dump($graphHook['metaData']);
			$_SESSION['timers']['GraphService_afterHookDayPoints'] =(microtime(true)-$_SESSION['timerBegin'] );
				
			
			$timestamp = Util::getSunInfo($config, $options['date']);
			$timestamp = array("beginDate"=>$timestamp['sunrise']-3600,"endDate"=>$timestamp['sunset']+3600);
		
			//var_dump($series);
			//var_dump(array_keys($graphHook['points']));
			$dataPoints = array();
			foreach ($series as $serie){
				if(array_key_exists($serie['name'], $graphHook['points'])){
					$dataPoints[$serie['name']]=$graphHook['points'][$serie['name']];
				}
			}
						
			if(isset($graphHook['timestamp']['beginDate']) AND $graphHook['timestamp']['beginDate']<  $timestamp['beginDate']){
				$timestamp['beginDate'] = $graphHook['timestamp']['beginDate'];
			}

			if(isset($graphHook['timestamp']['endDate']) AND $graphHook['timestamp']['endDate'] >  $timestamp['endDate']){
				$timestamp['endDate'] = $graphHook['timestamp']['endDate'];
			}
			$timestamp['endDate'] = $timestamp['endDate'] + 3600;
			$timestamp['beginDate'] = $timestamp['beginDate'] - 3600;
			
			$lang['generated'] = _('generated');
			$lang['max'] = _('max');
			
			$dtz = new DateTimeZone($config->timezone);
			$timezone = new DateTime('now', $dtz);
			$timezoneOffset = $dtz->getOffset( $timezone )/3600;
			
			return array('dataPoints'=>$dataPoints,'json'=>json_decode($graph->json),'series'=>$series,'axes'=>$axes,'axesList'=>$axesList,'source'=>'db','timestamp'=>$timestamp,'name'=>$graph->name,'options'=>$options,'slimConfig' => $slimConfig,'sunInfo'=>$sunInfo,'hideSeries'=>$hideSeries,'meta'=>$graphHook['metaData'],'lang'=>$lang,'timezoneOffset'=>$timezoneOffset);	
		}else{
			return array('json'=>json_decode($graph->json),'series'=>$series,'axes'=>$axes,'axesList'=>$axesList,'source'=>'db','name'=>$graph->name,'options'=>$options);
		}
	}

	public function getGraphAxe($id){
		$bObject = R::load(self::$tblAxes,$id);

		if ($bObject->id > 0) {
			$object = $this->toAxeObject($bObject);
				
		}
		return isset($object) ? $object : new GraphAxe();
	}



	public function getGraphSeries($id){
		$bObject = R::load(self::$tblSeries,$id);
		if ($bObject->id > 0) {
			$object = $this->toAxeObject($bObject);
		}
		return isset($object) ? $object : new GraphAxe();
	}


	public function getGraphAddonAxes($addon){
		$bObject = R::findAll(self::$tblAxes,' addon = :addon',array(':addon'=>$addon));
		if ($bObject->id > 0) {
			$object = $this->toAxeObject($bObject);
		}
		return isset($object) ? $object : new GraphAxe();
	}

	public function getGraphAllSeries(){
		$bObject = R::find(self::$tblSeries);
		if ($bObject->id > 0) {
			$object = $this->toSerieObject($bObject);
		}
		return isset($object) ? $object : new GraphSerie();
	}


	public function getGraphAddonSeries($addon){
		$bObject = R::findAll(self::$tblSeries,' addon = :addon',array(':addon'=>$addon));
		if ($bObject->id > 0) {
			$object = $this->toAxeObject($bObject);
		}
		return isset($object) ? $object : new GraphAxe();
	}


	private function toAxeBean($object, $bObject) {
		$bObject->id = $object->id;
		$bObject->label = $object->label;
		return $bObject;
	}

	private function toAxeObject($bObject) {
		$object = new GraphAxe();
		$object->id = $bObject->id;
		$object->label = $bObject->label;
		return $object;
	}
}
?>