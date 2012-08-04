<?php
class CsvDataAdapter extends CsvWriter implements DataAdapter {

    /**
     * write the live info to the file
     * @param int $invtnum
     * @param Live $live
     */
    public function writeLiveInfo($invtnum, Live $live) {
        $filename = Util::getLiveTXT($invtnum);
        $this->writeCsvData($filename, $this->getLiveCsvString($live));
    }

    /**
     * read the live info from an file
     * @param int $invtnum
     * @return Live
     */
    public function readLiveInfo($invtnum) {
        $filename = Util::getLiveTXT($invtnum);
        $lines = $this->readCsvData($filename);
        return $this->parseCsvToLive($lines[0]);
    }

    /**
     * will remove the live file
     */
    public function dropLiveInfo($invtnum) {
        unlink(Util::getLiveTXT($invtnum));
    }

    public function parseCsvToLive($csv) {
        // Convert comma to dot
        $csv = str_replace(",", ".", $csv);

        $fields = explode(";", $csv);
        $live = new Live();
        $live->SDTE = Util::getUTCdate($fields[0]) * 1000;
        $live->I1V = $fields[1];
        $live->I1A = $fields[2];
        $live->I1P = $fields[3];
        $live->I2V = $fields[4];
        $live->I2A = $fields[5];
        $live->I2P = $fields[6];
        $live->GV = $fields[7];
        $live->GA = $fields[8];
        $live->GP = $fields[9];
        $live->FRQ = $fields[10];
        $live->EFF = $fields[11];
        $live->INVT = $fields[12];
        $live->BOOT = $fields[13];
        $live->KWHT = $fields[14];

        return $live;
    }

    public function getLiveCsvString(Live $live) {
            $line = "" . $live->SDTE . ";" . $live->I1V . ";" . $live->I1A . ";" . $live->I1P . ";" .
                    $live->I2V . ";" . $live->I2A . ";" . $live->I2P . ";" . $live->GV . ";" . $live->GA . ";" . $live->GP . ";" .
                    $live->FRQ . ";" . $live->EFF . ";" . $live->INVT . ";" . $live->BOOT . ";" . $live->KWHT;
            return str_replace(".", ",", $line); // Convert the dots to comma
    }

    /**
     * write the max power today to the file
     * @param int $invtnum
     * @param MaxPowerToday $mpt
     */
    public function writeMaxPowerToday($invtnum, MaxPowerToday $mpt) {
        $filename = Util::getDataDir($invtnum)."/infos/pmaxotd.txt";
        $this->writeCsvData($filename, $this->getMaxPowerTodayCsvString($mpt));
    }

    /**
     * read the MaxPowerToday from an file
     * @param int $invtnum
     * @return MaxPowerToday
     */
    public function readMaxPowerToday($invtnum) {
        $filename = Util::getDataDir($invtnum)."/infos/pmaxotd.txt";
        $lines = $this->readCsvData($filename);
        if ($lines === false) {
            return new MaxPowerToday(); // File not found
        }
        return $this->parseCsvToMaxPowerToday($lines[0]);
    }

    /**
     * will remove the max power today file
     * @param int $invtnum
     */
    public function dropMaxPowerToday($invtnum) {
        unlink(Util::getDataDir($invtnum)."/infos/pmaxotd.txt");
    }

    public function parseCsvToMaxPowerToday($csv) {
        // Convert comma to dot
        $csv = str_replace(",", ".", $csv);

        $fields = explode(";", $csv);
        $mpt = new MaxPowerToday();
        $mpt->SDTE = Util::getUTCdate($fields[0]) * 1000;
        $mpt->GP = $fields[1];

        return $mpt;
    }

    public function getMaxPowerTodayCsvString(MaxPowerToday $mpt) {
        $line = "" . $mpt->SDTE . ";" . $mpt->GP;
        return str_replace(".", ",", $line); // Convert the dots to comma
    }

    /**
     * add the live info to the history
     * @param int $invtnum
     * @param Live $live
     * @param string date
     */
    public function addHistory($invtnum, Live $live, $date) {
        $filename = Util::getDataDir($invtnum) . "/csv/" . $date . ".csv";
        $this->appendCsvData($filename, $this->getLiveCsvString($live) . "\n");
    }

    /**
     * Read the history file
     * @param int $invtnum
     * @param string $date
     * @return array<Live> $live
     */
    public function readHistory($invtnum, $date) {
        $result = array();
        $filename = Util::getDataDir($invtnum)."/csv/" . $date . ".csv";
        $lines = $this->readCsvData($filename);
        foreach ($lines as $line) {
            $result[] = $this->parseCsvToLive($line);
        }
        return $result;
    }

    /**
     * Return the amount off history records
     * @param int $invtnum
     * @param string $date
     * @return int $count
     */
    public function getHistoryCount($invtnum, $date) {
        $filename = Util::getDataDir($invtnum)."/csv/" . $date . ".csv";
        $result = $this->readCsvData($filename);
        if ($result === false) {
            return 0; // File not found
        }
        return count($result);
    }

    /**
     * add an energy line
     * @param int $invtnum
     * @param MaxPowerToday $energy
     * @param int $year
     */
    public function addEnergy($invtnum, MaxPowerToday $energy, $year) {
        $filename = Util::getDataDir($invtnum) . "/production/energy" . $year . ".csv";
        $this->appendCsvData($filename, $this->getMaxPowerTodayCsvString($energy) . "\n");
    }

    /**
     * add the alarm to the events
     * @param int $invtnum
     * @param Alarm $alarm
     */
    public function addAlarm($invtnum, Alarm $alarm) {
        $filename = Util::getDataDir($invtnum) . "/infos/alarms.txt";
        $this->appendCsvData($filename, $this->getAlarmCsvString($alarm) . "\n");
    }

    public function getAlarmCsvString(Alarm $alarm) {
        return "" . $alarm->time . ";" . $alarm->alarm;
    }
}