<?php
class HookHandler {
	private static $instance;
	
	// Singleton
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new HookHandler();
		}
		return self::$instance;
	}
	
	// Class
	private $hooks;
	
	/**
	 * Constructor
	 */
    public function __construct() {
        $this->hooks = array();
    }
    
    /**
     * add the given hook
     * @param $name off the hook
     * @param $callback function
     * @throws InvalidArgumentException
     */
    public function add($name, $callback) {
        $this->hooks[$name][] = $callback;
    }
    
    /**
     * Get all listeners
     * @param $name off the hook
     * @return array:
     */
    public function getCallbacks($name) {
        return isset($this->hooks[$name]) ? $this->hooks[$name] : array();
    }
    
    /**
     * fire all hook listeners
     * @param $name off the hook
     */
    public function fire($name) {
        foreach($this->getCallbacks($name) as $callback) {
        	// Convert to class.method
            call_user_func($callback);
        }
    }
}