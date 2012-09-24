<?php
class PeriodHelper {
    /**
     * Check if the period has been passed
     * @param string $name
     * @param int $interval in minutes
     * @return boolean
     */
    public static function isPeriodJob($name, $interval) {
        $currenttime = time();
        $jobname = 'job_' . $name;
        $minute= date("i");
        if (($minute % $interval == 0)) {
            // Retrieve record
            $bean = R::findOne('cronjob', ' name = :name', array(':name'=>$jobname));
            if (!$bean){
                $bean = R::dispense('cronjob');
                $bean->name = $jobname;
                $bean->lastrun = 0;
            }

            if (($currenttime - $bean->lastrun) > ($interval * 60)) {
                // it's time do something
                $bean->lastrun = $currenttime;
                R::store($bean);
                return true;
            } else {
                // not yet time
                return false;
            }
        }
    }
}