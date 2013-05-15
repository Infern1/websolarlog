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
	public $conditionId;
	public $wind_speed;
	public $wind_direction;
	
	public function toBean($bObject) {
		$bObject->deviceId = $this->deviceId;
		$bObject->time = $this->time;
		$bObject->temp = $this->temp;
		$bObject->temp_min = $this->temp_min;
		$bObject->temp_max = $this->temp_max;
		$bObject->pressure = $this->pressure;
		$bObject->humidity = $this->humidity;
		$bObject->conditionId = $this->conditionId;
		$bObject->wind_speed = $this->wind_speed;
		$bObject->wind_direction = $this->wind_direction;
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
		$this->conditionId = $bObject->conditionId;
		$this->wind_speed = $bObject->wind_speed;
		$this->wind_direction = $bObject->wind_direction;
		return $this;
	}
}
?>