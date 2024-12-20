<?php

class Calendar
{
    private $year;
    private $month;
    private $holidays;
    private $moonPhases;
    private $dates;
    private $directoryName; // Used for Nice URL
    private $ajax = false;
    private $useAjax = true;
    private $useTidy = false;
    private $locale = 'en_US.UTF-8';
    private $encoding = 'UTF-8';
    private $locale_encoding = 'UTF-8';
    private $availableLocales = [
        'en' => 'en_US',
        'es' => 'es_ES', // Spanish
        'de' => 'de_DE', // German
        'ru' => 'ru_RU', // Russian
        'ja' => 'ja_JP', // Japanese
        'zh' => 'zh_CN', // Chinese
        'ko' => 'ko_KR', // Korean
    ];

    public function useAjax($bool)
    {
        $this->useAjax = $bool;
    }

    public function useTidy($bool)
    {
        $this->useTidy = $bool;
    }

    private function setLanguage($lang)
    {
        // Map language code to locale
        $this->locale = $this->availableLocales[$lang] ?? 'en_US';
    }

    private function formatDate($timestamp, $pattern)
    {
        $formatter = new IntlDateFormatter(
            $this->locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            date_default_timezone_get(),
            IntlDateFormatter::TRADITIONAL,
            $pattern
        );

        return $formatter->format($timestamp);
    }

    private function translateNiceUrlToParams()
    {
        $directoryName = dirname($_SERVER['SCRIPT_NAME']);
        if ($directoryName == DIRECTORY_SEPARATOR)
            $directoryName = '';
        $this->directoryName = $directoryName;
        $requestURI = $_SERVER['REQUEST_URI'];
        if (strlen($requestURI) > strlen($directoryName) && substr($requestURI, 0, strlen($directoryName)) == $directoryName)
            $requestURI = substr($requestURI, strlen($directoryName));
        $requestArray = explode('/', $requestURI);
        while (($piece = array_shift($requestArray)) !== null)
        {
            if (strlen($piece) == 4 && ctype_digit($piece))
                $_GET['year'] = $piece;
            elseif (strlen($piece) == 2 && ctype_alpha($piece))
                $_GET['language'] = $piece;
            elseif (ctype_digit($piece))
                 $_GET['month'] = $piece;
            elseif ($piece == 'ajax')
                 $this->ajax = true;
        }
    }

    public function __construct($year=null, $month=null, $language=null)
    {
        $this->translateNiceUrlToParams();

        if ($year === null)
            $year = isset($_GET['year']) ? $_GET['year'] : idate('Y');
        if ($month === null)
        {
            if (!isset($_GET['year']) && !isset($_GET['month']))
                $month = idate('n');
            elseif (isset($_GET['month']))
                $month = $_GET['month'];
        }
        if ($language === null)
            $language = isset($_GET['language']) ? $_GET['language'] : 'en';

        $this->setLanguage($language);

        // Year 2038 Bug
        if ($year >= 2038)
            $year = idate('Y');

        $this->year = $year;
        $this->month = $month;
        $this->setupMoonPhases();
        $this->setupHolidays();
    }

    private function setupMoonPhases()
    {
        $this->moonPhases = null;
        $calc = AstronomicalCalculation::getInstance();
        $calc->setYear($this->year);
        $this->moonPhases = $calc->calcMoonPhase();
    }

    private function setupHolidays()
    {
        $this->dates = array();

        $db = new PDO('sqlite:data/holidays.db');
        $db->exec("CREATE TABLE IF NOT EXISTS holiday (
                       id INTEGER PRIMARY KEY,
                       name TEXT,
                       after_solstice_or_equinox TEXT,
                       after_moon_phase TEXT,
                       day_of_week TEXT,
                       week_order TEXT,
                       week INTEGER,
                       month INTEGER,
                       day INTEGER,
                       day_offset INTEGER,
                       until_dst_value INTEGER
                   )
        ");
        $count = 0;
        $stmt = $db->prepare("select count(id) from holiday");
        if ($stmt && $stmt->execute())
            if ($row = $stmt->fetch(PDO::FETCH_NUM))
                $count = $row[0];
        if ($count == 0)
        {
            $handle = fopen('data/holidays.csv', 'r');
            $headers = fgetcsv($handle);
            $sql = 'INSERT INTO holiday (' . implode(', ', $headers) . ') VALUES(' . str_repeat('?, ', count($headers) - 1) . '?)';
            $stmt = $db->prepare($sql);
            while (($data = fgetcsv($handle)) !== false)
            {
                $values = array_pad($data, count($headers), null);
                $stmt->execute($values);
            }
            fclose($handle);
        }

        $stmt = $db->prepare('select * from holiday');
        if ($stmt && $stmt->execute())
        {
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Holiday');
            $this->holidays = $stmt->fetchAll();
        }

        foreach ($this->holidays as $holiday)
        {
            $date = $holiday->getDate($this->year, $this->moonPhases);
            if (!isset($this->dates[$date]))
                $this->dates[$date] = array();

            $this->dates[$date][] = $holiday->name;
        }
    }

    private function makeLink($overrides, $text, $useAjax=true)
    {
        $array = $_GET;
        foreach ($overrides as $name => $override)
            $array[$name] = $override;

        $year = isset($array['year']) ? $array['year'] : null;
        $month = isset($array['month']) ? $array['month'] : null;
        $language = isset($array['language']) ? $array['language'] : null;

        if ($this->useAjax)
            $ajax = $useAjax ? ' onclick="ajaxLoadLink(this.href); return false;"' : '';
        else
            $ajax = '';

        return '<a' . $ajax . ' href="' . $this->directoryName . (empty($year) ? '' : '/' . $year) . 
                                          (empty($month) ? '' : '/' . $month) . 
                                          (empty($language) ? '' : '/' . $language) . 
                                          '">' . $text . '</a>';
    }

    public function display($month=null)
    {
        if ($this->useTidy)
            ob_start();

        $baseURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $this->directoryName . '/';
        if (! $this->ajax)
        {
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>';
            echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/><title>Calendar</title>';
            echo '<link rel="shortcut icon" href="' . $this->directoryName . '/img/calendar_icon.png?' . filemtime('img/calendar_icon.png') . '" type="image/x-icon"/>';
            echo '<link type="text/css" rel="stylesheet" href="' . $this->directoryName . '/css/calendar.css?' . filemtime('css/calendar.css') . '"/>';
            echo '<link type="text/css" rel="stylesheet" href="' . $this->directoryName . '/css/print.css?' . filemtime('css/print.css') . '" media="print"/>';
            if ($this->useAjax)
                echo '<script type="text/javascript">var baseURL = "' . $baseURL . '";</script>';
            echo '</head><body>';
            echo '<div id="calendar">';
        }

        echo '<div id="header">';
        echo '<div id="logo">';
        echo '<a href="' . $this->directoryName . '/">';
        echo '<img src="' . $this->directoryName . '/img/calendar_icon.png?' . filemtime('img/calendar_icon.png') . '" alt=""/>';
        echo ' Calendar</a></div>';

        echo '<div id="languages">';

        echo $this->makeLink(array('language' => 'en'), 'English') . ' &nbsp;';
        echo $this->makeLink(array('language' => 'es'), 'Español') . ' &nbsp;';
        echo $this->makeLink(array('language' => 'de'), 'Deutsch') . ' &nbsp;';
        echo $this->makeLink(array('language' => 'ru'), urldecode('%D0%A0%D1%83%D1%81%D1%81%D0%BA%D0%B8%D0%B9')) . ' &nbsp;';
        echo $this->makeLink(array('language' => 'ja'), urldecode('%E6%97%A5%E6%9C%AC%E8%AA%9E')) . ' &nbsp;';
        echo $this->makeLink(array('language' => 'zh'), urldecode('%E4%B8%AD%E6%96%87')) . ' &nbsp;';
        echo $this->makeLink(array('language' => 'ko'), urldecode('%ED%95%9C%EA%B5%AD%EC%96%B4')) . ' &nbsp;';
        echo '</div></div>';

        echo '<h1>';

        echo '<span class="smallyear">';
        echo $this->makeLink(array('year' => $this->year - 1, 'month' => $this->month), $this->year - 1) . ' ';
        echo '</span> ';

        echo '<span class="year">';
        echo !empty($this->month) ? $this->makeLink(array('year' => $this->year, 'month' => null), $this->year) : $this->year;
        echo '</span>';

        echo ' <span class="smallyear">';
        echo $this->makeLink(array('year' => $this->year + 1, 'month' => $this->month), $this->year + 1) . ' ';
        echo '</span>';
        echo '</h1>';

        if (!empty($this->month))
            $month = $this->month;

        if ($month === null)
        {
            for ($i = 1; $i <= 12; $i++)
                $this->printMonth($i, $i > 1);
        }
        else
            $this->printMonth($month);

        echo '<h1 class="footer"><a href="https://github.com/phpdave11/php-calendar-lite">source code</a></h1>';

        if (! $this->ajax)
        {
            echo '</div>';
            if ($this->useAjax)
                echo '<script type="text/javascript" src="' . $this->directoryName . '/js/calendar.js?' . filemtime('js/calendar.js') . '"></script>';
            echo '</body></html>';
        }

        if ($this->useTidy)
        {
            $html = ob_get_clean();

            $config = array(
                'indent' => true,
                'output-xhtml' => true,
                'wrap' => 200);

            $tidy = new tidy;
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();

            echo $tidy;
        }
    }

    private function printMonth($month, $break=false)
    {
        $hour = 0;
        $minute = 0;
        $second = 0;
        $year = $this->year;
        $day = 1;

        $ts = mktime($hour, $minute, $second, $month, $day, $year);
        $days = idate('t', $ts);

        $today = date('Y-m-d');

        $monthName = $this->formatDate($ts, 'MMMM');
        $monthNumber = idate('n', $ts);

        $lastMonthTs = mktime($hour, $minute, $second, $month - 1, $day, $year);
        $nextMonthTs = mktime($hour, $minute, $second, $month + 1, $day, $year);

        $lastMonthName = $this->formatDate($lastMonthTs, 'MMMM');
        $lastMonthNumber = idate('n', $lastMonthTs);
        $lastYear = idate('Y', $lastMonthTs);

        $nextMonthName = $this->formatDate($nextMonthTs, 'MMMM');
        $nextMonthNumber = idate('n', $nextMonthTs);
        $nextYear = idate('Y', $nextMonthTs);

        $ts = mktime($hour, $minute, $second, $month, $day, $year);
        $start = date('l', $ts);
        while ($start != 'Sunday')
        {
            $ts = mktime($hour, $minute, $second, $month, --$day, $year);
            $start = date('l', $ts);
        }

        $link = empty($this->month) ? $this->makeLink(array('month' => $monthNumber), $monthName) : $monthName;

        echo '<div';
        if ($break)
            echo ' class="pb"';
        echo '>';

        echo '<div class="month right other">';
        if (!empty($this->month))
            echo $this->makeLink(array('month' => $lastMonthNumber, 'year' => $lastYear), $lastMonthName) . ' ';
        else
            echo '&nbsp;';
        echo '</div>';
        echo '<div class="month">' . $link;
        echo '<span class="printable"> ';
        echo $this->year;
        echo '</span>';
        echo '</div>';
        echo '<div class="month left other">';
        if (!empty($this->month))
            echo ' ' . $this->makeLink(array('month' => $nextMonthNumber, 'year' => $nextYear), $nextMonthName) . ' ';
        else
            echo '&nbsp;';
        echo '</div>';
        echo '<br/><br/>';
        echo '<table><thead><tr>';

        $weekDays = $day;
        $weekTs = mktime($hour, $minute, $second, $month, $weekDays, $year);
        for ($i = 0; $i < 7; $i++)
        {
            echo '<th>' . $this->formatDate($weekTs, 'EEEE') . '</th>';
            $weekTs = mktime($hour, $minute, $second, $month, ++$weekDays, $year);
        }
        echo '</tr></thead><tbody>';

        for ($i = 0; $i < 6; $i++)
        {
            echo '<tr>';
            for ($j = 0; $j < 7; $j++)
            {
                echo '<td>';
                if (idate('n', $ts) == $month)
                {
                    $date = date('Y-m-d', $ts);

                    if ($date == $today)
                        echo '<span class="today">' . idate('d', $ts) . '</span>';
                    else
                        echo idate('d', $ts);

                    // New Moon or Full Moon
                    if (isset($this->moonPhases[$date]))
                        echo ' <span>' . $this->moonPhases[$date][1] . '</span>';
                    echo '<br/>';

                    // Holiday
                    if (isset($this->dates[$date]))
                        foreach($this->dates[$date] as $data)
                            echo '<span class="holiday">' . $data . '</span><br/>';
                }
                else
                    echo '&nbsp;';
                $ts = mktime($hour, $minute, $second, $month, ++$day, $year);
                echo '</td>';
            }
            echo '</tr>';
            if (idate('n', $ts) != $month)
                break;
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}

?>
