### Unit 4. Databases & Entity-Attribute-Value (EAV)
#### 4.2.1. Echo the list of all store views and associated root categories.
1. Get a list of stores using: Magento\Store\Model\ResourceModel\Store\Collection
2. Get root category IDs using:
Magento\Store\Model\Store::getRootCategoryId()
3. Create a category collection and filter it by the root category IDs.
4. Add the category name attribute to the result.
5. Display stores with the associated root category names.
