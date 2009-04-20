<?php

class Halloween implements Holiday
{
    public function getName()
    {
        return 'Halloween';
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 10, 31, $year);
        return date('Y-m-d', $ts);
    }
}

?>
