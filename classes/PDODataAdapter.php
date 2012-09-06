<?php
class PDODataAdapter implements DataAdapter {
    function __construct() {
        R::setup();
    }

    function __destruct() {
        //print "Destroying " . $this->name . "\n";
    }

    /**
     * write the live info to the file
     * @param int $invtnum
     * @param Live $live
     */
    public function writeLiveInfo($invtnum, Live $live) {
        //Ready. Now insert a bean!
        $bean = R::dispense('Live');
        $bean->inverter = $invtnum;
        $bean->GA = $live->GA;
        $bean->GP = $live->GP;
        $bean->GV = $live->GV;

        //Store the bean
        $id = R::store($bean);

        return $id;
    }

    /**
     * read the live info from an file
     * @param int $invtnum
     * @return Live
     */
    public function readLiveInfo($invtnum) {
        $bean = R::load('Live',1 );

        $live = new Live();
        $live->GA = $bean->GA;
        $live->GP = $bean->GP;
        $live->GV = $bean->GV;

        return $live;
    }

    /**
     * will remove the live file
     */
    public function dropLiveInfo($invtnum) {

    }

    /**
     * write the max power today to the file
     * @param int $invtnum
     * @param MaxPowerToday $mpt
     */
    public function writeMaxPowerToday($invtnum, MaxPowerToday $mpt) {

    }

    /**
     * read the MaxPowerToday from an file
     * @param int $invtnum
     * @return MaxPowerToday
     */
    public function readMaxPowerToday($invtnum) {

    }

    /**
     * will remove the max power today file
     * @param int $invtnum
     */
    public function dropMaxPowerToday($invtnum) {

    }

    /**
     * add the live info to the history
     * @param int $invtnum
     * @param Live $live
     * @param string date
     */
    public function addHistory($invtnum, Live $live, $date) {

    }

    /**
     * Read the history file
     * @param int $invtnum
     * @param string $date
     * @return array<Live> $live
     */
    public function readHistory($invtnum, $date) {

    }

    /**
     * Return the amount off history records
     * @param int $invtnum
     * @param string $date
     * @return int $count
     */
    public function getHistoryCount($invtnum, $date) {

    }

    /**
     * add an energy line
     * @param int $invtnum
     * @param MaxPowerToday $energy
     * @param int $year
     */
    public function addEnergy($invtnum, MaxPowerToday $energy, $year) {

    }

    /**
     * add the alarm to the events
     * @param int $invtnum
     * @param Alarm $alarm
     */
    public function addAlarm($invtnum, Alarm $alarm) {

    }

    /**
     * Read the events file
     * @param int $invtnum
     * @return array<Alarm> $alarm
     */
    public function readAlarm($invtnum) {

    }

    public function writeDailyData($invtnum,Day $day) {

    }
    public function readDailyData($date, $invtnum) {

    }
    public function dropDailyData($invtnum) {

    }

    public function writeLastDaysData($invtnum,Day $day) {

    }
    public function readLastDaysData($date, $invtnum) {

    }
    public function dropLastDaysData($invtnum) {

    }
}