<?php

class AprilFoolsDay implements Holiday
{
    public function getName()
    {
        return 'April Fools Day';
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 4, 1, $year);
        return date('Y-m-d', $ts);
    }
}

?>
