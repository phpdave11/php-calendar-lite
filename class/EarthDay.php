<?php

class EarthDay implements Holiday
{
    public function getName()
    {
        return 'Earth Day';
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 4, 22, $year);
        return date('Y-m-d', $ts);
    }
}

?>
