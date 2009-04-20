<?php

class Thanksgiving implements Holiday
{
    public function getName()
    {
        return 'Thanksgiving';
    }

    public function getDate($year) // 4th Thursday in November (U.S.)
    {
        $day = 1;
        $ts = mktime(12, 0, 0, 11, $day, $year);
        while (date('l', $ts) != 'Thursday')
            $ts = mktime(12, 0, 0, 11, ++$day, $year);
        $ts = mktime(12, 0, 0, 11, $day + 21, $year); // +3  weeks
        return date('Y-m-d', $ts);
    }
}

?>
