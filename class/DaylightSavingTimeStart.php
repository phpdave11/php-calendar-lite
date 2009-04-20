<?php

class DaylightSavingTimeStart implements Holiday
{
    public function getName()
    {
        return 'Daylight Saving Time Starts';
    }

    public function getDate($year)
    {
        $day = 1;
        do
        {
            $ts = mktime(12, 0, 0, 1, $day++, $year);
        }
        while (date('I', $ts) != 1);
        return date('Y-m-d', $ts);
    }
}

?>
