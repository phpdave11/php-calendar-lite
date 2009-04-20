<?php

class WashingtonsBirthday implements Holiday
{
    public function getName()
    {
        return "Washington's Birthday";
    }

    public function getDate($year)
    {
        $day = 1;
        $ts = mktime(12, 0, 0, 2, $day, $year);
        while (date('l', $ts) != 'Monday')
            $ts = mktime(12, 0, 0, 2, ++$day, $year);
        $ts = mktime(12, 0, 0, 2, $day + 14, $year);
        return date('Y-m-d', $ts);
    }
}

?>
