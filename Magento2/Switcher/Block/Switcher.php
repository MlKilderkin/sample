<?php
namespace Mn\Switcher\Block;

class Switcher extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
								\Magento\Customer\Model\Session $session) {
		$this->_storeManager = $storeManager;
		$this->_session = $session;
	}

	public function getWebsites()
	{
		$_websites = $this->_storeManager->getWebsites();
		$_websiteData = array();
		foreach($_websites as $website){
			foreach($website->getStores() as $store){
				$wedsiteId = $website->getId();
				$storeObj = $this->_storeManager->getStore($store);
				$name = $website->getName();
				$code = $website->getCode();
				$url = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
				array_push($_websiteData, array('name' => $name,'url' => $url, 'code' => $code));
			}
		}

		return $_websiteData;
	}

	public function isLoggedIn() {
		if($this->_session->isLoggedIn()) {
			return true;
		}
		return false;
	}


	public function getCurrentStore() {
		return array(
			'name' => $this->_storeManager->getStore()->getName(),
			'code' => $this->_storeManager->getStore()->getCode(),
			'url' => $this->_storeManager->getStore()->getBaseUrl(),
		);
	}
}