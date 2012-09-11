<?php
interface DataAdapter {
    public function writeLiveInfo($invtnum, Live $live);
    public function readLiveInfo($invtnum);
    public function dropLiveInfo($invtnum);
    
    public function writeDailyData($invtnum,Day $day);
    public function readDailyData($date, $invtnum);
    public function dropDailyData($invtnum); 
      
    public function writeLastDaysData($invtnum,Day $day);
    public function readLastDaysData($date, $invtnum);
    public function dropLastDaysData($invtnum);
    
    public function writeMaxPowerToday($invtnum, MaxPowerToday $mpt);
    public function readMaxPowerToday($invtnum);
    public function dropMaxPowerToday($invtnum);
    
    public function addHistory($invtnum, Live $live, $date);
    public function readHistory($invtnum, $date);
    public function getHistoryCount($intnum, $date);
    
    public function writeInverterInfo($invtnum, InverterInfo $Oinfo);
    public function readInverterInfo($invtnum);
    public function dropInverterInfo($invtnum);    

    public function addEnergy($invtnum, MaxPowerToday $energy, $year);

    public function addEvent($invtnum, Event $Oevent);
    public function readEvent($invtnum);
    public function dropEvent($invtnum);
    
    public function writeLock(Lock $Olock);
    public function dropLock();
}
?>