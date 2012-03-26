<?php
/**
 * Class for handling actions related to a product
 */
class Application_Model_Product extends Admin2_Model_Abstract
{
    /**
     * Retrieve product data.
     *
     * @param string $oxid OXID of the product.
     *
     * @return array|null
     */
    public function getProduct($oxid)
    {
        /**
         * @var oxArticle $product
         */
        $product = oxNew('oxarticle');
        $product->disableLazyLoading();
        $product->loadInLang($this->currentLanguageId, $oxid);
        $productData = array();
        $fields = $product->getFieldNames();
        if (!$product->isLoaded()) {
            return null;
        }

        foreach ($fields as $field) {
            $productData[$field] = $product->getFieldData($field);
        }

        return $productData;
    }

    /**
     * Model-specific initialization code.
     *
     * @return null
     */
    public function init()
    {
        // Add your model-specific initialization code here, instead of overloading the constructor.
    }
}