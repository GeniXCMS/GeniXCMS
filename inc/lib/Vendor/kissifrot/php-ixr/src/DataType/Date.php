<?php

namespace IXR\DataType;

/**
 * IXR_Date
 *
 * @package IXR
 * @since 1.5.0
 */
class Date
{
    /** @var \DateTime */
    private $dateTime;

    function __construct($time)
    {
        // $time can be a PHP timestamp or an ISO one
        if (is_numeric($time)) {
            $this->parseTimestamp($time);
        } else {
            $this->parseIso($time);
        }
    }

    function parseTimestamp($timestamp)
    {
        $date = new \DateTime();
        $this->dateTime = $date->setTimestamp($timestamp);
    }

    function parseIso($iso)
    {
        $this->dateTime = \DateTime::createFromFormat(\DateTime::ATOM, $iso);
    }

    function getIso()
    {
        return $this->dateTime->format(\DateTime::ATOM);
    }

    function getXml()
    {
        return '<dateTime.iso8601>' . $this->getIso() . '</dateTime.iso8601>';
    }

    function getTimestamp()
    {
        return (int)$this->dateTime->format('U');
    }
}
