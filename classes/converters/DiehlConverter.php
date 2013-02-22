<?php
class DiehlConverter
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

        $live = new Live();
        $live->type = 'production';
        foreach (json_decode($inputLine) as $key => $value){
        	if(is_array($value)){
        		foreach ($value as $keys => $values){
			        $live->time = strtotime(date("d-m-Y H:i:s"));
			        if($values->path == 'eNEXUS_0066[s:17,t:1]' ){ $live->I1A = ($values->value);}
			        if($values->path == 'eNEXUS_0055[s:17,t:1]'){ $live->I1V = ($values->value);}
			        if($values->path == 'eNEXUS_0064[s:17,t:1]'){ $live->I1P = ($values->value);}
			        if($values->path == 'eNEXUS_0066[s:17,t:1,p:1]'){ $live->GA = ($values->value);}
			        if($values->path == 'eNEXUS_0064[s:17,t:1,p:1]'){ $live->GP = ($values->value);}
        			if($values->path == 'eNEXUS_0009[s:17,t:1,p:1]'){ $live->GV =($values->value);}
        			if($values->path == 'eNEXUS_0043[s:17,t:1]'){ $live->KWHT =($values->value);}
        			if($live->GP > 0 AND $live->I1P > 0){
	        			$live->EFF = ($live->GP / $live->I1P)*100;
        			}
        		}
        	}
        }
        
		//$live->FRQ = $data[10]; $live->INVT = $data[12]; $live->BOOT = $data[13];
		        
        // This line is only valid if GP and KWHT are filled with data
        if (empty($live->KWHT) || empty($live->GP)) {
        	return null;
        }

        return $live;
    }
}