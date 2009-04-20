<?php

class DaylightSavingTimeEnd implements Holiday
{
    public function getName()
    {
        return 'Daylight Saving Time Ends';
    }

    public function getDate($year)
    {
        $day = 1;
        do
        {
            $ts = mktime(12, 0, 0, 6, $day++, $year);
        }
        while (date('I', $ts) != 0);
        return date('Y-m-d', $ts);
    }
}

?>
