<?php

namespace Mn\Wall\Block;

use Magento\Framework\ObjectManagerInterface;
/**
 * Wall index block
 */
class Wall extends \Magento\Framework\View\Element\Template
{
	protected $_modelWallFactory;
	protected $_fileSystem;
	protected $_imageFactory;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Mn\Wall\Model\ResourceModel\Post\CollectionFactory $modelWallFactory,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Image\AdapterFactory $imageFactory,
		ObjectManagerInterface $objectManager,
		array $data = array()
	)
	{
		$this->objectManager = $objectManager;
		$this->_modelWallFactory = $modelWallFactory;
		$this->_fileSystem = $filesystem;
		$this->_imageFactory = $imageFactory;
		parent::__construct($context, $data);
	}

	public function getAllWalls() {
		return $this->_modelWallFactory->create()->addActiveFilter()->setOrder('created_at', 'DESC');;
	}

	public function getMediaUrl(){

		$media_dir = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
			->getStore()
			->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

		return $media_dir . 'mn/wall/post/image';
	}

	public function resize($image, $width = null, $height = null)
	{
		$absolutePath = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('mn/wall/post/image').$image;

		$imageResized = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/'.$width.'/').$image;
		//create image factory...
		$imageResize = $this->_imageFactory->create();
		$imageResize->open($absolutePath);
		$imageResize->constrainOnly(TRUE);
		$imageResize->keepTransparency(TRUE);
		$imageResize->keepFrame(FALSE);
		$imageResize->keepAspectRatio(TRUE);
		$imageResize->resize($width,$height);
		//destination folder
		$destination = $imageResized ;
		//save image
		$imageResize->save($destination);

		$resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$width.''.$image;
		return $resizedURL;
	}
}
