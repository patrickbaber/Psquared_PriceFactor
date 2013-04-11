<?php

class Psquared_PriceFactor_Block_Product_Price extends Mage_Catalog_Block_Product_Price
//class Psquared_PriceFactor_Block_Product_Price extends FireGento_GermanSetup_Block_Catalog_Product_Price
{
	public function getDisplayMinimalPrice(){
		if (Mage::getStoreConfig('catalog/price_factor/show_minimal_price') == 0){
			return false;
		}
		return parent::getDisplayMinimalPrice();
    }
}