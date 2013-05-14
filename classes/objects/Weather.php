<?php 

class Weather {
	public $id;
	public $deviceId;
	public $time;
	public $temp;
	public $temp_min; 
	public $temp_max; 
	public $pressure;
	public $humidity;
	
	public function toBean($bObject) {
		$bObject->id = $this->id;
		$bObject->deviceId = $this->deviceId;
		$bObject->time = $this->time;
		$bObject->temp = $this->temp;
		$bObject->temp_min = $this->temp_min;
		$bObject->temp_max = $this->temp_max;
		$bObject->pressure = $this->pressure;
		$bObject->humidity = $this->humidity;
		return $bObject;
	}

	public function toObject($bObject) {
		$this->id = $bObject->id;
		$this->deviceId = $bObject->deviceId;
		$this->time = $bObject->time;
		$this->temp = $bObject->temp;
		$this->temp_min = $bObject->temp_min;
		$this->temp_max = $bObject->temp_max;
		$this->pressure = $bObject->pressure;
		$this->humidity = $bObject->humidity;
		return $this;
	}
}
?>