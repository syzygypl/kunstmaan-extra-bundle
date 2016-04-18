<?php

namespace ArsThanea\KunstmaanExtraBundle\Intl;

use ArsThanea\KunstmaanExtraBundle\SiteTree\CurrentLocaleInterface;
use IntlDateFormatter;

class DateFormatter implements DateFormatterInterface
{
    /**
     * @var CurrentLocaleInterface
     */
    private $currentLocale;

    /**
     * @var array
     */
    private $formats;

    /**
     * @param CurrentLocaleInterface $currentLocale
     * @param array $formats
     */
    public function __construct(CurrentLocaleInterface $currentLocale, array $formats)
    {
        $this->currentLocale = $currentLocale;
        $this->formats = $formats;
    }


    /**
     * @param \DateTime $date
     * @param string $format
     * @return string
     */
    public function prettyDate(\DateTime $date, $format = "default")
    {
        $locale = $this->currentLocale->getCurrentLocale();

        if (isset($this->formats[$format][$locale])) {
            $format = $this->formats[$format][$locale];
        } elseif (isset($this->formats[$format]['default'])) {
            $format = $this->formats[$format]['default'];
        }

        $intlDateFormatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::FULL, $date->getTimezone()->getName(), null, $format);

        return $intlDateFormatter->format($date);

    }
}
