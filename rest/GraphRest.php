<?php
class GraphRest {
	private $graphService;
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->graphService = new GraphService();
	}
	
	/**
	 * Destructor
	 */
	function __destruct() {
		$this->deviceService = null;
	}

	public function DELETE($request, $options) {
		if($options[0]=="axe" && $options[1]>0){
			$graph = R::findOne('graph',' name = "daily" ');
			unset($graph->sharedAxe[$options[1]]);
			R::store($graph);
			return array('type'=>'axe','success'=>true);
		}
	}
	
	/**
	 * Rest functions 
	 */
	public function GET($request, $options) {
		$option = "";
		if (count($options) > 0) {
			$option = (trim($options[0]) != "") ? $options[0] : $option;
		}
		$id = -1;
		if (count($options) > 0) {
			$id = (trim($options[0]) != "") ? strtolower($options[0]) : $type;
		}
		if ($id > 0) {
			$graphOptions['deviceNum'] = $id;
			$graphOptions['mode'] = 'edit';
			$graphOptions['type'] = '';
			$data = $this->graphService->loadGraph($graphOptions);
			return $data;
		}

	}
	
	/**
	 * Rest functions
	 */
	public function POST($request, $options) {
		
	}
	
	public function getGraphs() {
		
		$result = array();
		foreach ($this->graphService->getAllGraphs() as $graph) {
			$result[] = array("id"=>$graph->id, "name"=>$graph->name);
		}
		return $result;
	}
	
	
}
?>