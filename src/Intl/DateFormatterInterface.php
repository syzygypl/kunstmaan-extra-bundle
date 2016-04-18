<?php

namespace ArsThanea\KunstmaanExtraBundle\Intl;

interface DateFormatterInterface
{
    /**
     * @param \DateTime $date
     * @param string $format
     * @return string
     */
    public function prettyDate(\DateTime $date, $format = "default");

}
