<?php
class LiveRest {
	private $weatherService;
	private $liveService;
	private $liveSmartMeterService;
        private $dataAdapter;
	
	/**
	 * Constructor
	 */
	function __construct() {
		$this->weatherService = new WeatherService();
		$this->liveService = new LiveService();
		$this->liveSmartMeterService = new LiveSmartMeterService();
                $this->dataAdapter = new PDODataAdapter();
	}
	
	/**
	 * Destructor
	 */
	function __destruct() {
		$this->liveSmartMeterService = null;
		$this->liveService = null;
		$this->weatherService = null;
	}

	/**
	 * Rest functions 
	 */
	
	public function GET($request, $options) {
		$result = array();
		
		$totalsProduction = array("devices"=>0,"GP"=>0,"GP2"=>0,"GP3"=>0, "GPOverall"=>0,"IPOverall"=>0,"gridV"=>0);
		$totalsMetering = array("devices"=>0,"liveEnergy"=>0, "meteringOverall"=>0);
		foreach (Session::getConfig()->devices as $device) {
			$type = $device->type;
			$live = null;
			switch ($type) {
				case "production":
					$live = $this->liveService->getLiveByDevice($device);
                                        if ($live === null){
                                            // No live record return, probably after an restart;
                                            $live = array(
                                                "GP"=>0,
                                                "GP2"=>0,
                                                "GP3"=>0, 
                                                "I1P"=>0,
                                                "I2P"=>0,
                                                "I3P"=>0,
                                                "GV"=>0,
                                                "trendImage"=>"equal",
                                                "trend"=>_("equal")
                                                );
                                            //break;
                                        } 
                                        foreach ($live as $key => $value){
                                            $live->$key = round($value,0);
                                        }
					$totalsProduction["devices"] = $totalsProduction["devices"] + 1;
					$totalsProduction["GP"] = $totalsProduction["GP"] + $live->GP;
					$totalsProduction["GP2"] = $totalsProduction["GP2"] + $live->GP2;
					$totalsProduction["GP3"] = $totalsProduction["GP3"] + $live->GP3;
                                        $totalsProduction["gridV"] = $live->GV;
                                        
                                        $live->GPTotal = round($live->GP + $live->GP2 + $live->GP3,0);
                                        $totalsProduction["GPOverall"] = round($totalsProduction["GPOverall"] + $live->GP + $live->GP2 + $live->GP3,0);
                                        
                                        $live->IPTotal = round($live->I1P + $live->I2P + $live->I3P,0);
                                        $totalsProduction["IPOverall"] = round($totalsProduction["IPOverall"] + $live->I1P + $live->I2P + $live->I3P,0);
                                        
                                        $avgPower = $this->dataAdapter->readCache("","index","live",$device->id,"trend");
					$live->trendImage = $avgPower[0]['value'];
					$live->trend = _($live->trendImage);
                                        
                                        if(Util::isSunDown(-300)){
                                            $live->status = 'offline';
                                        }else{
                                            $live->status = 'online';
                                        }
                                        
					break;
				case "metering":
					$live = $this->liveSmartMeterService->getLiveByDevice($device);
					$totalsMetering["devices"] = $totalsMetering["devices"] + 1;
					$totalsMetering["liveEnergy"] = $totalsMetering["liveEnergy"] + $live->liveEnergy;
					$totalsMetering["meteringOverall"] = round($totalsMetering["meteringOverall"] +$totalsMetering["liveEnergy"],2); 
					break;
				case "weather":
					$live = $this->weatherService->getLastWeather($device);					
					break;
			}
			$result[] = array("type"=>$type, "id"=>$device->id, "name"=>$device->name, "data"=>$live);
		}
                if($totalsProduction["IPOverall"]>0 and $totalsProduction["GPOverall"]>0){
                    $totalsProduction['EFFTotal'] = round((($totalsProduction["GPOverall"] / $totalsProduction["IPOverall"]) * 100),2);
                }else{
                    $totalsProduction['EFFTotal'] = round(0,2);
                }
                // Get Gauge Max
		$avgGP = $this->dataAdapter->getAvgPower(Session::getConfig());
		($avgGP['recent']<=0) ? $avgGPRecent = 1 : $avgGPRecent = $avgGP['recent'];
		$gaugeMaxPower = ceil( ( ($avgGPRecent*1.1)+100) / 100 ) * 100;
		$result['maxGauges'] = $gaugeMaxPower;
                
		$lang = array();
                $lang['DCPower'] = _("DC Power");
		$lang['ACPower'] = _("AC Power");
                $lang['Efficiency'] = _("Efficiency");
                $lang['usage'] 	= _("Usage");
                
		$result["totals"] = array("production"=>$totalsProduction, "metering"=>$totalsMetering, "overallUsage" =>($totalsProduction["GPOverall"]+$totalsMetering["meteringOverall"]));
                $result["lang"] = $lang;
		return $result;
	}
	
	public function getCategory() {
		return new CategoryRest();
	}

	/**
	 * Non rest functions
	 */
	public function loadProduct($id) {
		return $this->productService->get($id);
	}
}
?>