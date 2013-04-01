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
        $dataSplit = preg_split("/\n/",$inputLine);

		foreach ($dataSplit as $value) {
			// remove the connection tries from the array
			if(substr($value,0,10) != 'Connecting'){
		    	$data[] = $value;
			}
		}

        // Check if the record is okay
        if (trim($data[count($data)-1]) != "Done.") {
            return null;
        }
        $live = new Live();
        $live->type = 'production';
        if (!empty ($data[13])) {
        	$live->time = trim(substr($data[13],14,22));
        }
        if (!empty ($data[21])) {
        	$live->status = trim(substr($data[21],20,2));
        }
        if (!empty ($data[23])) {
        	$live->KWHDay = trim(substr($data[23],8,6));
        }
        if (!empty ($data[24])) {
            $live->KWHT = trim(substr($data[24],8,8));
        }
        
        if (!empty ($data[28])) {
            $live->I1V = trim(substr($data[28],31,7));
        }
        if (!empty ($data[28])) {
            $live->I1A = trim(substr($data[28],47,6));
        }
        if (!empty ($data[28])) {
            $live->I1P = trim(substr($data[28],16,6))*1000;
        }
        
        if (!empty ($data[29])) {
            $live->I2V = trim(substr($data[29],31,7));
        }
        if (!empty ($data[29])) {
            $live->I2A = trim(substr($data[29],47,6));
        }
        if (!empty ($data[29])) {
            $live->I2P = trim(substr($data[29],16,6))*1000;
        }
        
        if (!empty ($data[31])) {
            $live->GV = trim(substr($data[31],32,6));
        }
        if (!empty ($data[31])) {
            $live->GA = trim(substr($data[31],46,7));
        }
        
        if (!empty ($data[31])) {
            $live->GP = trim(substr($data[31],16,6))*1000;
        }
        
        ($live->I1P && $live->I2P) ? $live->IP = $live->I1P+$live->I2P : $live->IP = $live->I1P;

		if($live->GP > 0 AND $live->IP > 0){
        	// We can calculate the Efficienty DC>AC in %
			$live->EFF = ($live->GP/$live->IP)*100;
        }

        if (!empty ($data[35])) {
            $live->FRQ = trim(substr($data[35],12,6));
        }
        
		/* Not available
        if (!empty ($data[12])) {
            $live->INVT = $data[12];
        }
        if (!empty ($data[13])) {
            $live->BOOT = $data[13];
        }*/

        //var_dump($live);
        // This line is only valid if GP and KWHT are filled with data
        if (empty($live->KWHT) || empty($live->GP)) {
        	return null;
        }

        return $live;
    }
}