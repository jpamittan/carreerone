<?php

namespace App\Models\Factories;

/**
 * Helper class for formatting content
 */
class Format {
    /**
     * static Function to format reponse text to browser
     * @param string $content
     * @return string $content
     */
    public static function responseText($content) {
        return $content;
    }

    /**
     * Static function to validate date is valid with the format passed
     * @param  string $date
     * @param  string $format
     * @return boolean
     */
    public static function validateDate($date, $format) {
        if (empty($date) || empty($format)) {
            return false;
        }
        $dt = \DateTime::createFromFormat($format, $date);
        if ($dt) {
            return true;
        }
        return false;
    }

    /**
     * static Function to display date as month year format
     * @param string $content
     * @return string $content
     */
    public static function displayMonthYear($date, $format = 'Y-m-d') {
        // validate date
        if (!self::validateDate($date, $format)) {
            return '';
        }
        $date_parts = explode('-', $date);
        $dt = \DateTime::createFromFormat($format, $date);
        return $dt->format('F Y');
    }

    /**
     * static Function to display date as mm/yy format
     * @param string $content
     * @return string $content
     */
    public static function displayMonthYearAlt($date, $format = 'Y-m-d') {
        // validate date
        if (!self::validateDate($date, $format)) {
            return '';
        }
        $date_parts = explode('-', $date);
        $dt = \DateTime::createFromFormat($format, $date);
        return $dt->format('m/y');
    }

    /**
     * Function to display date as formatted
     * @param string $content
     * @return string $content
     */
    public static function displayDate($date, $format = 'Y-m-d') {
        // validate date
        if (!self::validateDate($date, $format)) {
            return '';
        }

        $dt = \DateTime::createFromFormat($format, $date);
        //return $dt->format('d/m/Y');
        return $dt->format('jS M  Y');
    }

    /**
     * Function to display date time as formatted
     * @param string $content
     * @return string $content
     */
    public static function displayDateTime($datetime, $format = 'Y-m-d H:i:s') {
        if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
            return '';
        }
        $dt = \DateTime::createFromFormat($format, $datetime);
        return $dt->format('jS M  Y');
    }

    /**
     * static Function to get day of a date
     * @param string $content
     * @return string $content
     */
    public static function day($date, $format = 'Y-m-d') {
        // validate date
        if (!self::validateDate($date, $format)) {
            return null;
        }
        $date_parts = explode('-', $date);
        $dt = \DateTime::createFromFormat($format, $date);
        return ltrim($dt->format('d'), '0');
    }

    /**
     * static Function to get month of a date
     * @param string $content
     * @return string $content
     */
    public static function month($date, $format = 'Y-m-d') {
        // validate date
        if (!self::validateDate($date, $format)) {
            return null;
        }
        $date_parts = explode('-', $date);
        $dt = \DateTime::createFromFormat($format, $date);
        return ltrim($dt->format('m'), '0');
    }

    /**
     * Function to get year of a date
     * @param string $content
     * @return string $content
     */
    public static function year($date, $format = 'Y-m-d') {
        // validate date
        if (!self::validateDate($date, $format)) {
            return null;
        }
        $date_parts = explode('-', $date);
        $dt = \DateTime::createFromFormat($format, $date);
        return $dt->format('Y');
    }

    /**
     * static function to convert passed in dt of format df1 from timezone tz1 to timezone tz2 with format df2
     * @param datetime $dt
     * @param string $tz1
     * @param string $df1
     * @param string $tz2
     * @param string $df2
     * @return datetime
     */
    public static function dateConvert($dt, $tz1, $df1, $tz2, $df2) {
        try {
            // shortcut for current dt
            if ($dt == 'now') {
                $dt = date($df1);
            }
            // create DateTime object
            $d = \DateTime::createFromFormat($df1, $dt, new \DateTimeZone($tz1));
            if ($d) {
                // convert timezone
                $d->setTimeZone(new \DateTimeZone($tz2));

                // convert dateformat
                return $d->format($df2);
            } else {
                return "";
            }
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * static Function to check whether a particular date has been passed
     * @param date $date
     * @return boolean
     */
    public static function hasPassed($date, $format = 'Y-m-d H:i:s') {
        // consider null value as not passed
        if (is_null($date) || empty($date)) {
            return true;
        }

        // validate date
        if (!self::validateDate($date, $format)) {
            return false;
        }
        $dt = \DateTime::createFromFormat($format, $date);
        $now = new \DateTime();
        return !$dt->diff($now)->invert;
    }

    /**
     * static Function to combine year, month and day to form a date
     * @param integer $year
     * @param integer $month
     * @param integer $day
     */
    public static function createDate($year, $month = 1, $day = 1) {
        if (empty($year)) {
            return null;
        }

        if (is_null($month)) {
            $month = 1;
        }
        if (is_null($day)) {
            $day = 1;
        }
        $date = $year . '-' . $month . '-' . $day;

        // validate date
        if (self::validateDate($date, 'Y-m-d')) {
            return $date;
        }
        return null;
    }

    /**
     * static Function to create date from string YYYY-MM-DD
     * @param string $date
     */
    public static function createDateFromString($date) {
        $dates = explode('-', $date);

        $year = array_get($dates, 0, null);
        $month = array_get($dates, 1, null);
        $day = array_get($dates, 2, null);

        return self::createDate($year, $month, $day);
    }

    /**
     * static funciton to compare end date is not before start date
     * start date and end date can be same
     * @param Date $end_date
     * @param Date $date
     */
    public static function compareDates($date1, $date2, $format = 'Y-m-d') {
        if (!self::validateDate($date1, $format) || !self::validateDate($date2, $format)) {
            return false;
        }

        $dt_1 = \DateTime::createFromFormat($format, $date1);
        $dt_2 = \DateTime::createFromFormat($format, $date2);

        return !$dt_2->diff($dt_1)->invert;
    }

    /**
     * [slugify description]
     * @param string $content
     * @return string $content
     */
    public static function slugify($content) {
        $content = preg_replace('/\s/', '_', $content);
        return $content;
    }

    /**
     * Returns rating class based on score value passed in
     * @param  string $score
     * @return string ratingClass
     */
    public static function stars($score) {
        if (is_null($score)) {
            return null;
        }
        $score = number_format(round($score, 1), 1);
        $ratings = array(
            '0.1' => 'stars-half',
            '0.2' => 'stars-1',
            '0.3' => 'stars-1-half',
            '0.4' => 'stars-2',
            '0.5' => 'stars-2-half',
            '0.6' => 'stars-3',
            '0.7' => 'stars-3-half',
            '0.8' => 'stars-4',
            '0.9' => 'stars-4-half',
            '1.0' => 'stars-5',
        );
        return array_get($ratings, $score, null);
    }
}
