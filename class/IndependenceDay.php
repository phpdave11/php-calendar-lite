<?php

class IndependenceDay implements Holiday
{
    public function getName()
    {
        return 'Independence Day';
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 7, 4, $year);
        return date('Y-m-d', $ts);
    }
}

?>
