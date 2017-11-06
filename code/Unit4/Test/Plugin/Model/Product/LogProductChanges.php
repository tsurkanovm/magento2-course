<?php
namespace Unit4\Test\Plugin\Model\Product;

use Psr\Log\LoggerInterface;
use \Magento\Catalog\Model\ResourceModel\Product;
use \Magento\Framework\DataObject ;

class LogProductChanges
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LogPageOutput constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeSave(
        Product $subject,
        DataObject $object
    ) {
        // @todo here need more complex function for generated data that contains old value and new value
       $result = array_udiff_assoc($object->getData(), $object->getOrigData(),
           function ($a,$b){
               if (is_array($a) or is_array($b) or is_object($a) or is_object($b)) {
                   return false;
               }

               return $a !== $b;
           }
           );

        $this->logger->debug(json_encode($result));
    }
}
