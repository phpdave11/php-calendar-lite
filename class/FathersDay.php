<?php

class FathersDay implements Holiday
{
    public function getName()
    {
        return "Father's Day";
    }

    public function getDate($year)
    {
        $day = 1;
        $ts = mktime(12, 0, 0, 6, $day, $year);
        while (date('l', $ts) != 'Sunday')
            $ts = mktime(12, 0, 0, 6, ++$day, $year);
        $ts = mktime(12, 0, 0, 6, $day + 14, $year);
        return date('Y-m-d', $ts);
    }
}

?>
