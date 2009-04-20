<?php

class Christmas implements Holiday
{
    public function getName()
    {
        return 'Christmas';
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 12, 25, $year);
        return date('Y-m-d', $ts);
    }
}

?>
