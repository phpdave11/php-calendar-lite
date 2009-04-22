<?php

class MothersDay implements Holiday
{
    public function getName()
    {
        return "Mother's Day";
    }

    public function getDate($year)
    {
        $day = 1;
        $ts = mktime(12, 0, 0, 5, $day, $year);
        while (date('l', $ts) != 'Sunday')
            $ts = mktime(12, 0, 0, 5, ++$day, $year);
        $ts = mktime(12, 0, 0, 5, $day + 7, $year);
        return date('Y-m-d', $ts);
    }
}

?>
