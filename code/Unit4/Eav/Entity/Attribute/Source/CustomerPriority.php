<?php
/**
 * Created by PhpStorm.
 * User: mihail
 * Date: 09.11.17
 * Time: 14:07
 */

namespace Unit4\Eav\Entity\Attribute\Source;


use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class CustomerPriority extends AbstractSource
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        $options = array_map(function($priority) {
            return [
                'label' => sprintf('Priority %d', $priority),
                'value' => $priority
            ];
        }, range(1, 10));
        if ($this->getAttribute()->getFrontendInput() === 'select') {
            array_unshift($options, ['label' => '', 'value' => 0]);
        }
        return $options;
    }
}
