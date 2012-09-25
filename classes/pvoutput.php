<?php
/**
 * TODO :: Convert to a plugin class
 * Need to think about a generic way for it
 */


if ($PVOUTPUT==true && $invtnum==$NUMINV) {// PVoutput
                //$myFile=(dirname(dirname(__FILE__)) . "/data/pvoutput_return.txt");
                //$fh = fopen($myFile, 'w+') or die("can't open $myFile file");

                for ($i=1;$i<=$NUMINV;$i++) {
                    $historyDay = $dataAdapter->readHistory(1, date('Ymd'));
                    $HDFirstLine = reset($historyDay); // HistoryDayFirstLine
                    $HDLastLine = end($historyDay);    // HistoryDayLastLine

                    // get time of first and last record of date...
                    $HDFirstDateTime = UTIL::getUTCdate($HDFirstLine['SDTE']);
                    $HDLastDateTime = UTIL::getUTCdate($HDLastLine['SDTE']);

                    // get current UTC date/time
                    $now = strtotime(date("Ymd H:i"));

                    // check is last line is not older then 300 sec.
                    if ($now-$HDLastDateTime>300){ // too old
                        // set to 0
                        $KWHD=0;
                        $GP=0;
                    } else {
                        // get KWHT first line
                        $KWHT_strt=str_replace(",", ".",$HDFirstLine['KWHT']);
                        // get KWHT last line line
                        $KWHT_stop=str_replace(",", ".",$HDLastLine['KWHT']);
                        // calculate KWHD (D of Daily?!) kwhStart - kwhStop = Wh today multiplied by 1000 to get kWh, correted and round by 0 decimals
                        $KWHD=round((($KWHT_stop-$KWHT_strt)*1000*$config->CORRECTFACTOR ),0); //Wh
                        // GridPower = GridPower of last line rounded by 0 decimals
                        $GP=round(str_replace(",", ".",$HDLastLine['GP']),0);
                    }
                    // sum the inverter values
                    $KWHDtot=$KWHDtot+$KWHD;
                    $GPtot=$GPtot+$GP;
                    // get the $stringData
                    $stringData="#$i $KWHD Wh $GP W\n";
                    fwrite($fh, $stringData);
                } // end of multi
                $now = date("Ymd");
                $time= date('H:i',mktime(date("H"), date("i")-1, 0, date("m") , date("d") , date("Y")));
                $INVT = round(str_replace(",", ".",$INVT),1);
                $GV = round(str_replace(",", ".",$GV),1);

                $pvoutput = shell_exec('curl -d "d='.$now.'" -d "t='.$time.'" -d "c1=0" -d "v1='.$KWHDtot.'" -d "v2='.$GPtot.'" -d "v5='.$INVT.'" -d "v6='.$GV.'" -H "X-Pvoutput-Apikey: '.$APIKEY.'" -H "X-Pvoutput-SystemId: '.$SYSID.'" http://pvoutput.org/service/r2/addstatus.jsp &');

                $stringData="\nSend : $now $time - $KWHDtot Wh $GPtot W $INVT C $GV V\nPVoutput returned: $pvoutput";
                //fwrite($fh, $stringData);

                $OEvent->INV = $INVT;
                $OEvent->SDTE = date("Ymd-H:m:s");
                $OEvent->type = 'PVoutput returned';
                // TODO :: not sure if $new_content is the right thing to save in DB;
                $OEvent->event = $stringData;
                $dataAdapter->addEvent($invtnum, $event);

                //fclose($fh);
            } // End of once PVoutput feed
?>