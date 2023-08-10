<?php

namespace ML\DeveloperTest\Model\Attribute\Backend;

class Countries extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    public function beforeSave($object)
    {
        $excluded_countries_array = $object->getExcludedCountries();
        if (is_array($excluded_countries_array)) {
            $object->setExcludedCountries(implode(',', $excluded_countries_array));
        }
        return $this;
    }

    public function afterLoad($object)
    {
        $excluded_countries_string = $object->getExcludedCountries();
        if (!empty($excluded_countries_string)) {
            $object->setExcludedCountries(explode(',', $excluded_countries_string));
        }
        return $this;
    }
}
