<?php

class PiDay implements Holiday
{
    public function getName()
    {
        return '<span style="font-family:Times New Roman;font-size:22px">' . urldecode('%CF%80') . '</span> Day';
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 3, 14, $year);
        return date('Y-m-d', $ts);
    }
}

?>
