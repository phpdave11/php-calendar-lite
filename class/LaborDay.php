<?php

class LaborDay implements Holiday
{
    public function getName()
    {
        return 'Labor Day';
    }

    public function getDate($year)
    {
        $day = 1;
        $ts = mktime(12, 0, 0, 9, $day, $year);
        while (date('l', $ts) != 'Monday')
            $ts = mktime(12, 0, 0, 9, ++$day, $year);
        return date('Y-m-d', $ts);
    }
}

?>
