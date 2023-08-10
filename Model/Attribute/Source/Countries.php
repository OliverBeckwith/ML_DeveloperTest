<?php

namespace ML\DeveloperTest\Model\Attribute\Source;

use Magento\Directory\Model\Country;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class Countries extends AbstractSource implements OptionSourceInterface
{
    private $countries;

    public function __construct(
        Country $countryModel
    ) {
        $this->countries = $countryModel->getResourceCollection()->load()->toOptionArray();
    }

    /**
     * Get list of all available countries
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->countries;
    }
}
