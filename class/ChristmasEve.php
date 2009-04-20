<?php

class ChristmasEve implements Holiday
{
    public function getName()
    {
        return 'Christmas Eve';
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 12, 24, $year);
        return date('Y-m-d', $ts);
    }
}

?>
