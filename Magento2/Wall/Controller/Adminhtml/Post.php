<?php

namespace Mn\Wall\Controller\Adminhtml;

abstract class Post extends \Magento\Backend\App\Action
{
    /**
     * Post Factory
     * 
     * @var \Mn\Wall\Model\PostFactory
     */
    protected $_postFactory;

    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Result redirect factory
     * 
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $_resultRedirectFactory;

    /**
     * constructor
     * 
     * @param \Mn\Wall\Model\PostFactory $postFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mn\Wall\Model\PostFactory $postFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->_postFactory           = $postFactory;
        $this->_coreRegistry          = $coreRegistry;
        $this->_resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Init Post
     *
     * @return \Mn\Wall\Model\Post
     */
    protected function _initPost()
    {
        $postId  = (int) $this->getRequest()->getParam('wall_id');
        /** @var \Mn\Wall\Model\Post $post */
        $post    = $this->_postFactory->create();
        if ($postId) {
            $post->load($postId);
        }
        $this->_coreRegistry->register('mn_wall_post', $post);
        return $post;
    }
}
