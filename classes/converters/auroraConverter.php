<?php
class auroraConverter
{

    /**
     * Converts the result of getData to an Live object
     * @param string $inputLine
     * @return Live or null
     */
    public static function toLive($inputLine)
    {
        // Split on a serie of spaces (not one)
        $data = preg_split("/[[:space:]]+/",$inputLine);

        // Check if the record is okay
        if (!empty($data[22]) && trim($data[22]) != "OK") {
            return null;
        }

        $live = new Live();

        if (!empty ($data[0])) {
            $live->SDTE = $data[0];
            $live->logtime = strtotime($data[0]);
        }
        if (!empty ($data[1])) {
            $live->I1V = $data[1];
        }
        if (!empty ($data[2])) {
            $live->I1A = $data[2];
        }
        if (!empty ($data[3])) {
            $live->I1P = $data[3];
        }
        if (!empty ($data[4])) {
            $live->I2V = $data[4];
        }
        if (!empty ($data[5])) {
            $live->I2A = $data[5];
        }
        if (!empty ($data[6])) {
            $live->I2P = $data[6];
        }
        if (!empty ($data[7])) {
            $live->GV = $data[7];
        }
        if (!empty ($data[8])) {
            $live->GA = $data[8];
        }
        if (!empty ($data[9])) {
            $live->GP = $data[9];
        }
        if (!empty ($data[10])) {
            $live->FRQ = $data[10];
        }
        if (!empty ($data[11])) {
            $live->EFF = $data[11];
        }
        if (!empty ($data[12])) {
            $live->INVT = $data[12];
        }
        if (!empty ($data[13])) {
            $live->BOOT = $data[13];
        }
        if (!empty ($data[19])) {
            $live->KWHT = $data[19];
        }

        return $live;
    }
}