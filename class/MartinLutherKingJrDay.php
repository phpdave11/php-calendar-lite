<?php

class MartinLutherKingJrDay implements Holiday
{
    public function getName()
    {
        return 'Martin Luther King, Jr. Day';
    }

    public function getDate($year)
    {
        $day = 1;
        $ts = mktime(12, 0, 0, 1, $day, $year);
        while (date('l', $ts) != 'Monday')
            $ts = mktime(12, 0, 0, 1, ++$day, $year);
        $ts = mktime(12, 0, 0, 1, $day + 14, $year);
        return date('Y-m-d', $ts);
    }
}

?>
