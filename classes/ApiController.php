<?php 
/**
 * This ApiController will handle the api requests.
 * It supports most off the restfull service methods.
 * 
 * - PUT
 * - POST
 * - GET
 * - DELETE
 * - HEAD*
 * - OPTIONS*
 * - TRACE* 
 * - CONNECT*
 * 
 * At the moment the ones with an * are not supported
 * 
 * @author Martin Diphoorn
 * @version 1.0.0
 */
class ApiController
{
	// Singleton
	private static $instance;
	
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new ApiController(); 
		}
		return self::$instance;
	}
	// End singleton
	
	/**
	 * Start the routing based on the incoming method type and the request path
	 */
	public function route() {
		$method = $_SERVER['REQUEST_METHOD'];
		$request = (isset($_SERVER['PATH_INFO'])) ? explode("/", substr($_SERVER['PATH_INFO'], 1)) : "";
		
		return $this->routeRequest($method, $request);
	}
	
	/**
	 * Route the request
	 * @param $method
	 * @param $request
	 * @param $index
	 */
	public function routeRequest($method, $request) {
		if ($request == "" || $request == "/" || $request == "//") {
			return array("error"=>"Invalid request");
		}
		
		$requestPath = array_values($request); // Copy orignal array, we dont want to modify it
		$classname = array_shift($requestPath) . "Rest"; // Get the first entry and remove it from the array
		
		// Create the first object
		$object = new $classname();
		
		// Loop to the last getter we can found
		for ($pathId = 1; $pathId < count($request)+1; $pathId++) {
			$methodObject = "";
			if (count($request) > 1) {
				$methodObject = $request[$pathId];
				$propertyName = "get" . lcfirst($methodObject);
			}
			
			// Check if it exists
			if (trim($methodObject) === "" || !method_exists($object, $propertyName)) {
				if (!method_exists($object, $method)) {
					return array("error"=>"This restfull service does not support this method");					
				}
				return $object->$method($request, $requestPath);
			}
			
			$object = $object->$propertyName();
			array_shift($requestPath);
		}
		
		return null;
	}
	
	
}
?>