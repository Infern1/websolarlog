<?php
interface DeviceApi {
	/**
	 * Returns the state off the inverter
	 * We currently support the folowing states:
	 *   -   0 = Try to detect
	 *   -   1 = Offline
	 *   -   9 = Online
	 */
	public function getState();
	
    public function getAlarms();
    public function getData();
    public function getInfo();
    public function syncTime();
    public function getHistoryData();
}
?>