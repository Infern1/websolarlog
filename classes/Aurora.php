<?php
Class Aurora {
    private $ADR;
    private $PORT;
    private $INVTNUM;
    private $COMOPTION;
    private $DEBUG;

    function __construct($ADR, $PORT, $INVTNUM, $COMOPTION, $DEBUG) {
        $this->ADR = $ADR;
        $this->PORT = $PORT;
        $this->INVTNUM = $INVTNUM;
        $this->COMOPTION = $COMOPTION;
        $this->DEBUG = $DEBUG;
    }

    public function getAlarms() {
    	if ($this->DEBUG) {
    		return "W2223424".rand(0,9);
    	} else {
    		return $this->execute('-A');
    	}
        
    }

    public function getData() {
        if ($this->DEBUG) {
            //return $this->execute('-b -c -T ' . $this->COMOPTION . ' -d0 -e 2>'. Util::getErrorFile($this->INVTNUM));
            return date("Ymd")."-11:11:11 233.188904 6.021501 1404.147217 234.981598 5.776632 1357.402222 242.095657 10.767704 2585.816406 59.966419 93.636436 68.472496 41.846001 3.230 8441.378 0.000 8384.237 12519.938 14584.0 84 236.659 OK";
        } else {
            return $this->execute('-c -T ' . $this->COMOPTION . ' -d0 -e');
        }
    }

    public function getInfo() {
        if ($this->DEBUG) {
            return "PowerOne XXXXXX.XXXXXXXX";
        } else {
           return $this->execute('-p -n -f -g -m -v');
        }
    }

    /*
    public function getEnergyInfo() {
        return $this->execute('-e');
    }
    */

    public function syncTime() {
        return $this->execute('-L');
    }

    private function execute($options) {
        return shell_exec(AURORA . ' -a' . $this->ADR . ' ' . $options . ' ' . $this->PORT);
    }

}
?>