<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\Intl\DateFormatterInterface;

class PrettyDateTwigExtension extends \Twig_Extension
{
    /**
     * @var DateFormatterInterface
     */
    private $dateFormatter;

    /**
     * @param DateFormatterInterface $dateFormatter
     */
    public function __construct(DateFormatterInterface $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }


    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('pretty_date', [$this, 'prettyDate']),
        ];
    }

    /**
     * @param \DateTime|string $date
     * @param string           $format
     *
     * @return string
     */
    public function prettyDate($date, $format = null)
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        if (null !== $format) {
            return $this->dateFormatter->prettyDate($date, $format);
        }

        return $this->dateFormatter->prettyDate($date);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pretty_date_twig';
    }
}
