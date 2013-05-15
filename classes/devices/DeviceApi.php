<?php
interface DeviceApi {
	/**
	 * Returns the state off the device
	 * We currently support the folowing states:
	 *   -   0 = Try to detect
	 *   -   1 = Offline
	 *   -   9 = Online
	 */
	public function getState();
	
	/**
	 * Try to retrieve an alarm from the device
	 */
    public function getAlarms();
    
    /**
     * Get live data from the device
     */
    public function getData();
    
    /**
     * Get information of the device like the model, type, serial, etc
     */
    public function getInfo();
    
    /**
     * Try to synchronize the time
     */
    public function syncTime();
    
    /**
     * Try to retrieve the history data off the device
     */
    public function getHistoryData();
}
?>