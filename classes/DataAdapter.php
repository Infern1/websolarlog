<?php
interface DataAdapter {
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
}
?>