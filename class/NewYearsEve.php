<?php

class NewYearsEve implements Holiday
{
    public function getName()
    {
        return "New Year's Eve";
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 12, 31, $year);
        return date('Y-m-d', $ts);
    }
}

?>
