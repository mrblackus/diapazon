<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 19/12/2013
 * Time: 15:35
 */

namespace Diapazon;

class DateUtils
{
    /**
     * @param string $sInput
     * @return string
     * @throws \Exception
     */
    public static function timestampWithTimezoneFromDDMMYYYY($sInput)
    {
        $datetime = \DateTime::createFromFormat('d/m/Y', $sInput);
        if ($datetime !== false)
            return date('r', $datetime->getTimestamp());
        else
            throw new \Exception('INVALID_DATE_FORMAT');
    }

    /**
     * @param $sInput
     * @return \DateTime
     * @throws \Exception
     */
    public static function DateTimeFromTimestampWithTimezone($sInput)
    {
        $datetime = \DateTime::createFromFormat('Y-m-d H:i:sO', $sInput);
        if (!is_object($datetime))
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s.uO', $sInput);
        if (!is_object($datetime))
            $datetime = \DateTime::createFromFormat('D, d M Y H:i:s O', $sInput);

        if (is_object($datetime))
            return $datetime;
        else
            throw new \Exception('INVALID_DATE_FORMAT');
    }

    /**
     * @param $sInput
     * @return bool|string
     * @throws \Exception
     */
    public static function dateFromDDMMYYYY($sInput)
    {
        $datetime = \DateTime::createFromFormat('d/m/Y', $sInput);
        if ($datetime !== false)
            return date('Y-m-d', $datetime->getTimestamp());
        else
            throw new \Exception('INVALID_DATE_FORMAT');
    }

    /**
     * @param $sInput
     * @return bool|string
     * @throws \Exception
     */
    public static function DDMMYYYYFromDate($sInput)
    {
        $datetime = \DateTime::createFromFormat('Y-m-d', $sInput);
        if ($datetime !== false)
            return date('d/m/Y', $datetime->getTimestamp());
        else
            throw new \Exception('INVALID_DATE_FORMAT');
    }

    /**
     * @param $sInput
     * @return string
     * @throws \Exception
     */
    public static function frenchDateFromTimestampWithTimezone($sInput)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:sO', $sInput);
        if (is_object($dateTime))
        {
            setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
            return strftime('%e %h %G', $dateTime->getTimestamp());     //UNIX ONLY
            //return strftime('%d %b %Y', $dateTime->getTimestamp());   //WINDOWS ONLY
        }
        else
            throw new \Exception('INVALID_DATE_FORMAT');
    }
} 