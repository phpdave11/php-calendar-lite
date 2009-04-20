<?php

class ValentinesDay implements Holiday
{
    public function getName()
    {
        return "Valentine's Day";
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 2, 14, $year);
        return date('Y-m-d', $ts);
    }
}

?>
