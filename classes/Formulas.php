<?php
/**
 * BaseResult class file
 *
 * PHP Version 5.2
 *
 * @category  Classes
 * @package   Results
 * @author    Martin Diphoorn <martin@diphoorn.com>
 * @license   http://www.diphoorn.com/licenses/license.php Commercial License
 * @link      http://www.diphoorn.com/
 */

/**
 * BaseResult
 *
 * @category  Classes
 * @package   Results
 * @author    Martin Diphoorn <martin@diphoorn.com>
 * @license   http://www.diphoorn.com/licenses/license.php Commercial License
 * @link      http://www.diphoorn.com/
 * @access    public
 * @since     File available since Release 0.0.0
 */
class Formulas
{

    /**
     * Calculate the Power Efficency
     * @param $gridPower
     * @param $COEF
     */
    public static function calcPowerEfficency($gridPower, $COEF, $decimals = 2) {
        $COEF = ($COEF > 1) ? 1 : $COEF; // Only allow efficency of max 100%
        return round($gridPower * $COEF, $decimals);
    }

    /**
     * Calculates the KiloWattHours for this day
     * @param $kiloWattHourStart
     * @param $kiloWattHourStop
     * @param $COEF
     */
    public static function calcKiloWattHourDay($kiloWattHourStart, $kiloWattHourStop, $COEF, $decimals = 0) {
        return round( (($kiloWattHourStop - $kiloWattHourStart) * 1000 * $COEF) / 1000, $decimals);
    }

    /**
     * Calculates the average power over a given time
     * @param $kiloWattHourStart
     * @param $kiloWattHourStop
     * @param $timeDifference
     * @param $decimals
     */
    public static function calcAveragePower($kiloWattHourStart, $kiloWattHourStop, $timeDifference, $decimals = 1) {
        return round((((($kiloWattHourStop-$kiloWattHourStart) * 3600) / $timeDifference) * 1000), $decimals);
    }


}

?>