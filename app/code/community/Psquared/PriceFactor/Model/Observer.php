<?php

class Psquared_PriceFactor_Model_Observer
{
	/**
	 * Modify prices on catagory page
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function catchCatalogProductCollectionLoadAfter($observer){
		if (Mage::getStoreConfig('catalog/price_factor/enable_price_factor') == 1){
			foreach ($observer->getEvent()->getCollection() as $product){
				$this->_adjustProductPrice($product);
			}
		}
	}

	/**
	 * Modify prices on product detail page
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function catchCatalogProductLoadAfter($observer){
		if (Mage::getStoreConfig('catalog/price_factor/enable_price_factor') == 1){
			$product = $observer->getEvent()->getProduct();
			$this->_adjustProductPrice($product);
		}
	}

    /**
     * Detect store view change to clear cart
     *
     * @param Varien_Event_Observer $observer
     */
    public function catchControllerActionPredispatch($observer){
        if (Mage::getStoreConfig('catalog/price_factor/clear_cart_on_store_view_switch') == 1){
            $fromStore = $_GET['___from_store'];
            $store = $_GET['___store'];
            if (!empty($fromStore) && !empty($store)) {
                foreach(Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item){
                    Mage::getSingleton('checkout/cart')->removeItem($item->getId() )->save();
                }
            }
        }
    }

    /**
     * Adjustment for price, special price, final price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product
     */
	protected function _adjustProductPrice($product){
        $adjustment = Mage::getStoreConfig('catalog/price_factor/price_factor');

		$price = $product->getPrice();
		$specialPrice = $product->getSpecialPrice();
		$finalPrice = $product->getFinalPrice();
			
		$finalPrice = $this->_adjustPrice($adjustment, $finalPrice);
		$product->setFinalPrice($finalPrice);
			
		//recalculate the special price, if this is set
		if (!empty($specialPrice)){
			$specialPrice = $this->_adjustPrice($adjustment, $specialPrice);
			$product->setSpecialPrice($specialPrice);
		}
			
		$price = $this->_adjustPrice($adjustment, $price);
		$product->setPrice($price);
		
		return $product;
	}

	/**
	 * Price value adjustment
	 *
	 * @param float $adjustment
	 * @param float $price
	 * @return float
	 */
	protected function _adjustPrice($adjustment, $price){
		if ($adjustment > 0){
			$price *= $adjustment;
		}

		return $price;
	}
}