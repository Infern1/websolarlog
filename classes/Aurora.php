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
        return $this->execute('-A');
    }

    public function getData() {
        if ($this->DEBUG) {
            return $this->execute('-b -c -T ' . $this->COMOPTION . ' -d0 -e 2>'. Util::getErrorFile($this->INVTNUM));
        } else {
            return $this->execute('-c -T ' . $this->COMOPTION . ' -d0 -e');
        }
    }

    public function getInfo() {
        return $this->execute('-p -n -f -g -m -v');
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