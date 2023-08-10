<?php

namespace ML\DeveloperTest\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use ML\DeveloperTest\Helper\Ip2Country;

class BlockAddToCartByCountry
{
    private Ip2Country $ip2Country;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        Ip2Country $ip2Country,
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->ip2Country = $ip2Country;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundAddItem(
        \Magento\Quote\Model\Quote $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $enabled = (bool) $this->scopeConfig->getValue("catalog/country_exclusions/enable");
        if (!$enabled)
            return $proceed($item);

        // Magic function to get the 'excluded_countries' custom attribute
        $excluded_countries = $item->getProduct()->getExcludedCountries();
        // If no excluded_countries present on product, don't waste time fetching the user's country
        if (empty($excluded_countries) || count($excluded_countries) < 1)
            return $proceed($item);

        $user_country = $this->ip2Country->getCurrentCountryCode();
        // Since our default is to allow unless specified, if IP country lookup fails: allow
        if (empty($user_country))
            return $proceed($item);

        if (in_array(strtoupper($user_country['code']), $excluded_countries)) {
            //Excluded
            $warning_msg = $this->scopeConfig->getValue("catalog/country_exclusions/warning_message");
            $warning_msg = str_replace("COUNTRY_NAME", $user_country['name'], $warning_msg);
            throw new LocalizedException(__($warning_msg));
        }
        return $proceed($item);
    }
}
