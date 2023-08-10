<?php

namespace ML\DeveloperTest\Plugin;

use Magento\Framework\Exception\LocalizedException;
use ML\DeveloperTest\Helper\Ip2Country;

class BlockAddToCartByCountry
{
    private Ip2Country $ip2Country;

    public function __construct(
        Ip2Country $ip2Country,
    ) {
        $this->ip2Country = $ip2Country;
    }

    public function aroundAddItem(
        \Magento\Quote\Model\Quote $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        // Magic function to get the 'excluded_countries' custom attribute
        $excluded_countries = $item->getProduct()->getExcludedCountries();
        // If no excluded_countries present on product, don't waste time fetching the user's country
        if (empty($excluded_countries))
            return $proceed($item);

        $user_country = $this->ip2Country->getCurrentCountryCode();
        // Since our default is to allow unless specified, if IP country lookup fails: allow
        if (empty($user_country))
            return $proceed($item);

        // Neaten the user input of excluded countries to an uppercase array
        $excluded_countries = array_map('trim', explode(',', strtoupper($excluded_countries)));
        if (in_array(strtoupper($user_country['code']), $excluded_countries)) {
            //Excluded
            throw new LocalizedException(__("User country excluded from product. Not adding to cart"));
        }
        return $proceed($item);
    }
}
