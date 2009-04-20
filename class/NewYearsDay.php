<?php

class NewYearsDay implements Holiday
{
    public function getName()
    {
        return "New Year's Day";
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 1, 1, $year);
        return date('Y-m-d', $ts);
    }
}

?>
