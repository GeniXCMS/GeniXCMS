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

    public function __construct($time)
    {
        // $time can be a PHP timestamp or an ISO one
        if (is_numeric($time)) {
            $this->parseTimestamp($time);
        } else {
            $this->parseIso($time);
        }
    }

    private function parseTimestamp($timestamp)
    {
        $date = new \DateTime();
        $this->dateTime = $date->setTimestamp($timestamp);
    }

    private function parseIso($iso)
    {
        $formats = [
            \DateTime::ATOM,
            /* The older version of IXR_Date (which is still used in e.g. WordPress) does not parse
             * iso8601 dates using DateTime::createFromFormat(DateTime::ATOM,..),
             * but using substr() with fixed offsets.
             * The parser does not expect dashes between year, month and day.
             * To be compatible with wordpress clients, lets mimic the behaviour here. */
            // with timezone
            'Ymd\TH:i:sP',
            'Ymd\THisP',
            // without timezone
            'Ymd\TH:i:s',
            'Ymd\THis',
        ];

        foreach ($formats as $format) {
            $this->dateTime = \DateTime::createFromFormat($format, $iso);
            if ($this->dateTime !== false) {
                break;
            }
        }
    }

    public function getIso()
    {
        return $this->dateTime->format(\DateTime::ATOM);
    }

    public function getXml()
    {
        return '<dateTime.iso8601>' . $this->getIso() . '</dateTime.iso8601>';
    }

    public function getTimestamp()
    {
        return (int)$this->dateTime->format('U');
    }
}
