<?php

class Utils{


    public function logMessage($file, $uniqueID, $data)
    {
        //Replace the newlines
        $date = date("Y-m-d G:i:s");
        if ($fo = fopen($file, 'ab')) {
            fwrite($fo, "$date | $uniqueID | $data\n");
            fclose($fo);
        }

    }
}
