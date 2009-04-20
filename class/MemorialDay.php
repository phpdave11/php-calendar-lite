<?php

class MemorialDay implements Holiday
{
    public function getName()
    {
        return 'Memorial Day';
    }

    public function getDate($year)
    {
        $day = 31;
        $ts = mktime(12, 0, 0, 5, $day, $year);
        while (date('l', $ts) != 'Monday')
            $ts = mktime(12, 0, 0, 5, --$day, $year);
        return date('Y-m-d', $ts);
    }
}

?>
