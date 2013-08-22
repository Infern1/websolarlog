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

		$axe_exist = R::load('axes',1);

		
		
		if ($graph || $reset == true || $axe_exist){
			if($graph->json=='null' || $reset == true || $axe_exist){

				R::exec( "DROP TABLE IF EXISTS axes;" );
				R::exec( "DROP TABLE IF EXISTS axe;" );
				
				R::exec( "DROP TABLE IF EXISTS axes_graph;" );
				R::exec( "DROP TABLE IF EXISTS axe_graph;" );
				R::exec( "DROP TABLE IF EXISTS graph;" );
				R::exec( "DROP TABLE IF EXISTS graph_series;" );
				R::exec( "DROP TABLE IF EXISTS graph_serie;" );
				R::exec( "DROP TABLE IF EXISTS series;" );
				R::exec( "DROP TABLE IF EXISTS serie;" );
				
				$graph = null;
			}
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
		$graphBean->sharedAxes = array_merge(self::defaultAxes(),$defaultAxes);
		
		R::store($graphBean);
		
		$graphBean = R::findOne('graph',' name = "daily" ');
		
		$links = $graphBean->ownGraph_series;
		$i=0;
		foreach($links as $link){
			$link->show = true;
			$link->disabled = false;
			$link->name = $series[$i]->name;
			$link->json = $series[$i]->json;
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
		$serie[1]['json'] = json_encode(array('label'=>'Avg Power (W)','yaxis'=>'yaxis'));
		$serie[1]['name'] = 'avgPowerW';
		$serie[1]['show'] = $show;
		$serie[1]['disabled'] = 'false';
		$serie[1]['addon'] = 'wsl';
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
		$axe[0]['AxeOrder'] = 1;
		$axe[0]['addon'] = 'wsl';
		$axe[1]['json'] = json_encode(array('axe'=>'y2axis','label'=>'Cum Power (W)','min'=>0,'labelRenderer'=>'CanvasAxisLabelRenderer'));
		$axe[1]['show'] = $show;
		$axe[1]['AxeOrder'] = 2;
		$axe[1]['addon'] = 'wsl';
		$axe[2]['json'] = json_encode(
				array('axe'=>'xaxis','label'=>'','renderer'=>'DateAxisRenderer','tickRenderer'=>'CanvasAxisTickRenderer','labelRenderer'=>'CanvasAxisLabelRenderer','tickInterval'=>3600,
						'tickOptions'=>array('formatter'=>'DayDateTickFormatter','angle'=>-45)));
		$axe[2]['show'] = $show;
		$axe[2]['AxeOrder'] = 0;
		$axe[2]['addon'] = 'wsl';
		return $axe;
	}
	
	public  function getAllGraphs(){
		return R::find('graph');
	}
	
	public function getGraph($id){
		return R::exportAll(R::find('graph',' id = :id ',array(':id'=>$id)));
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
		$series = $graph->ownGraph_series;
		$_SESSION['timers']['GraphService_After_OwnGraph_series'] =(microtime(true)-$_SESSION['timerBegin'] );
		
		$_SESSION['timers']['GraphService_Before_exportAll_series'] =(microtime(true)-$_SESSION['timerBegin'] );
		//var_dump($ownGraphSeries);
		//$series = R::exportAll($ownGraphSeries);
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
						if($serie['disabled']){
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
				$i++;
			}
			
		}

		$series = $seriesNew;

		// find axes of graph
		
		$axes = $graph->with(' ORDER BY AxeOrder ASC ')->sharedAxes;
		$_SESSION['timers']['GraphService_sharedAxes'] =(microtime(true)-$_SESSION['timerBegin'] );
		
		$axesList = array();
		$i=0;
		$x=0;
		$y=0;
		foreach ($axes as $axe){
			
			$json = json_decode($axe['json']);
			$axesList[$i]['axe'] = $json->axe;
			
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
			
			$slimConfig['inverters'] = $inverters;
			
			$util = new Util();
			
			$sunInfo = $util->getSunInfo($config, $options['date']);
			
			
			// get data of graph
			$graphDataService = new GraphDataService();
			
			$_SESSION['timers']['GraphService_BeforeLoadGraphData'] =(microtime(true)-$_SESSION['timerBegin'] );
			$graphData = $graphDataService->loadData($options);
			$_SESSION['timers']['GraphService_AfterLoadGraphData'] =(microtime(true)-$_SESSION['timerBegin'] );
			
			
			$_SESSION['timers']['GraphService_beforeHookDayPoints'] =(microtime(true)-$_SESSION['timerBegin'] );
			$graphDataHook = HookHandler::getInstance()->fire("GraphDayPoints",$options['deviceNum'],$options['date'],$options['type'],$disabledSeries);
			$_SESSION['timers']['GraphService_afterHookDayPoints'] =(microtime(true)-$_SESSION['timerBegin'] );
			//var_dump($dataPoints->points);
			
			if(isset($graphDataHook->points) AND is_array($graphDataHook->points)){
				$dataPoints = array_merge($graphData->points,$graphDataHook->points);
			}else{
				$dataPoints = $graphData->points;
			}
			//var_dump($dataPoints);
			
			//set timestamp to overrule standard timestamp
			$timestamp = Util::getSunInfo($config, $options['date']);
			$timestamp = array("beginDate"=>$timestamp['sunrise']-3600,"endDate"=>$timestamp['sunset']+3600);
			
			if(isset($graphDataHook->metaData) and $graphDataHook->metaData['timestamp']){
				$timestamp = array("beginDate"=>$graphDataHook->metaData['timestamp']['beginDate']-3600,"endDate"=>$graphDataHook->metaData['timestamp']['endDate']+3600);
			}
			return array('dataPoints'=>$dataPoints,'json'=>json_decode($graph->json),'series'=>$series,'axes'=>$axes,'axesList'=>$axesList,'source'=>'db','timestamp'=>$timestamp,'name'=>$graph->name,'options'=>$options,'slimConfig' => $slimConfig,'sunInfo'=>$sunInfo,'hideSeries'=>$hideSeries);
						
					
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