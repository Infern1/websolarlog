<?php
class SMABlueToothConverter
{

    /**
     * Converts the result of getData to an Live object
     * @param string $inputLine
     * @return Live or null
     */
    public static function toLive($inputLine)
    {
        // Check if the input line is valid
        if ($inputLine == null || trim($inputLine) == "") {
            return null;
        }

        // Split on a serie of spaces (not one)
        $data = preg_split("/\n/",$inputLine);
		var_dump($data);
        // Check if the record is okay
        if (trim($data[count($data)-1]) != "Done.") {
            return null;
        }

        $live = new Live();
        $live->type = 'production';
        if (!empty ($data[14])) {
        	$live->time = trim(substr($data[14],14,22));
        }
        if (!empty ($data[22])) {
        	$live->status = trim(substr($data[22],20,2));
        }
        if (!empty ($data[24])) {
        	$live->KWHDay = trim(substr($data[24],15,6));
        }
        if (!empty ($data[25])) {
            $live->KWHT = trim(substr($data[25],15,8));
        }
        
        if (!empty ($data[29])) {
            $live->I1V = trim(substr($data[29],22,7));
        }
        if (!empty ($data[29])) {
            $live->I1A = trim(substr($data[29],38,7));
        }
        if (!empty ($data[29])) {
            $live->I1P = trim(substr($data[29],53,7));
        }
        
        if (!empty ($data[30])) {
            $live->I2V = trim(substr($data[30],22,7));
        }
        if (!empty ($data[30])) {
            $live->I2A = trim(substr($data[30],38,7));
        }
        if (!empty ($data[30])) {
            $live->I2P = trim(substr($data[30],53,7));
        }
        
        if (!empty ($data[32])) {
            $live->GV = trim(substr($data[32],22,7));
        }
        if (!empty ($data[32])) {
            $live->GA = trim(substr($data[32],38,7));
        }
        
        if (!empty ($data[32])) {
            $live->GP = trim(substr($data[32],53,7));
        }
        /* Not supported by WSL
        if (!empty ($data[33])) {
        	$live->GV = trim(substr($data[33],22,7));
        }
        if (!empty ($data[33])) {
        	$live->GA = trim(substr($data[33],38,7));
        }
        if (!empty ($data[33])) {
        	$live->GP = trim(substr($data[33],53,7));
        }
        */
        /* Not supported by WSL
        if (!empty ($data[34])) {
        	$live->GV = trim(substr($data[34],22,7));
        }
        if (!empty ($data[34])) {
        	$live->GA = trim(substr($data[34],38,7));
        }
        if (!empty ($data[34])) {
        	$live->GP = trim(substr($data[34],53,7));
        }
        */
        if (!empty ($data[36])) {
            $live->FRQ = trim(substr($data[36],12,6));
        }
        
		/* Not available
        if (!empty ($data[12])) {
            $live->INVT = $data[12];
        }
        if (!empty ($data[13])) {
            $live->BOOT = $data[13];
        }*/

        
        // This line is only valid if GP and KWHT are filled with data
        if (empty($live->KWHT) || empty($live->GP)) {
        	return null;
        }

        return $live;
    }
}