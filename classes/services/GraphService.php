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
		HookHandler::getInstance()->fire("onDebug", "Run GraphService::janitorDbCheck->installGraph");
		$graph = R::findOne('graph',' name = "daily" ');
		
		if ($graph || $reset == true){
			if($graph->json=='null' || $reset == true){
				R::exec( 'DROP TABLE IF EXISTS axe;' );
				R::exec( 'DROP TABLE IF EXISTS axe_graph;' );
				R::exec( 'DROP TABLE IF EXISTS graph;' );
				R::exec( 'DROP TABLE IF EXISTS graph_serie;' );
				R::exec( 'DROP TABLE IF EXISTS serie;' );
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
		$graphBean->sharedSerie = array_merge(self::defaultSeries(),$defaultSeries);
		
		$defaultAxes = HookHandler::getInstance()->fire("defaultAxes");
		$graphBean->sharedAxe = array_merge(self::defaultAxes(),$defaultAxes);
		
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
		$serie = R::dispense('serie',2);
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
		$axe = R::dispense('axe',3);
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
		$dataPoints=array();
		
		$config = Session::getConfig();
		$graph = R::findOne('graph',' name = "daily" ');
				
		// translate hideSerie labels
		$graphJson = json_decode($graph->json);
		$i=0;

		$timestamp = array("beginDate"=>strtotime("06:00")-3600,"endDate"=>strtotime("21:00")+3600);
			
		
		// find series of graph
		$series = R::exportAll($graph->sharedSerie);
		//translate serie labels 
		$seriesNew = array();
		$disabledSeries = array();
		$i=0;
		$ii=0;
		foreach ($series as $serie){

			$json = json_decode($serie['json']);
			$json->label = _($json->label);

			// check if we need this 
			if($options['mode']=='frontend'){
				if($serie['disabled']=='false'){
					// we want to show this serie.
						$seriesNew[$i]['json'] = json_encode($json);
						$seriesNew[$i]['show'] = $serie['show'];
					$i++;
				}else{
					// we DON'T want to show this serie
					$disabledSeries[$ii] = $json->label;
					$ii++;
				}
			}else{
				$seriesNew[$i]['json'] = $json;
				$seriesNew[$i]['disabled'] = $serie['disabled'];
				$seriesNew[$i]['show'] = $serie['show'];
				$i++;
			}
			
		}
		$series = $seriesNew;

		// find axes of graph
		$axes = R::exportAll($graph->with(' ORDER BY AxeOrder ASC ')->sharedAxe);
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
			// get data of graph
			$graphDataService = new GraphDataService();
			
			$dataPoints = $graphDataService->loadData($options);
			$dataPointsHook = HookHandler::getInstance()->fire("GraphDayPoints",$options['deviceNum'],$options['date'],$options['type'],$disabledSeries);
	
			
			//var_dump($dataPointsHook);
			if(is_array($dataPointsHook->points)){
				$dataPoints = array_merge($dataPoints,$dataPointsHook->points);
			}	
			//var_dump((array)json_decode($graph->json));
			if(is_array($dataPointsHook->metaData)){
				$graph->json = json_encode(array_merge((array)json_decode($graph->json),$dataPointsHook->metaData));
			}
			
			//set timestamp to overrule standard timestamp
			$timestamp = Util::getSunInfo($config, time());
			$timestamp = array("beginDate"=>$timestamp['sunrise']-3600,"endDate"=>$timestamp['sunset']+3600);
			
			if($dataPointsHook->timestamp){
				$timestamp = array("beginDate"=>$dataPointsHook->timestamp['beginDate']-3600,"endDate"=>$dataPointsHook->timestamp['endDate']+3600);
			}
		}
		return array('dataPoints'=>$dataPoints,'json'=>json_decode($graph->json),'series'=>$series,'axes'=>$axes,'axesList'=>$axesList,'source'=>'db','timestamp'=>$timestamp,'name'=>$graph->name);
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