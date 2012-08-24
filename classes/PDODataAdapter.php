<?php
class PDODataAdapter extends CsvWriter implements DataAdapter {

    public function writeLiveInfo($invtnum, Live $live);
    public function readLiveInfo($invtnum);
    public function dropLiveInfo($invtnum);

    public function writeMaxPowerToday($invtnum, MaxPowerToday $mpt);
    public function readMaxPowerToday($invtnum);
    public function dropMaxPowerToday($invtnum);

    public function addHistory($invtnum, Live $live, $date);
    public function readHistory($invtnum, $date);
    public function getHistoryCount($intnum, $date);

    public function addEnergy($invtnum, MaxPowerToday $energy, $year);

    public function addAlarm($invtnum, Alarm $alarm);
    public function readAlarm($invtnum);


    /**
     * write the live info to the file
     * @param int $invtnum
     * @param Live $live
     */
    public function writeLiveInfo($invtnum, Live $live) {

    }

    /**
     * read the live info from an file
     * @param int $invtnum
     * @return Live
     */
    public function readLiveInfo($invtnum) {

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
}