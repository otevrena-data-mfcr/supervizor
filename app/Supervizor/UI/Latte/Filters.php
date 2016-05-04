<?php

namespace Supervizor\UI\Latte;

class Filters
{

    public function formatPrice($price)
    {
        return $this->formatNumber($price) . ' Kč'; //!FIXME Locales
    }



    public function formatNumber($number)
    {
        return number_format($number, 0, ',', ' '); //!FIXME Locales
    }
}
