<?php

namespace Mn\Wall\Controller\Adminhtml\Post;

class Delete extends \Mn\Wall\Controller\Adminhtml\Post
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('wall_id');
        if ($id) {
            $name = "";
            try {
                /** @var \Mn\Wall\Model\Post $post */
                $post = $this->_postFactory->create();
                $post->load($id);
                $name = $post->getName();
                $post->delete();
                $this->messageManager->addSuccess(__('The Post has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_mn_wall_post_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mn_wall/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mn_wall_post_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('mn_wall/*/edit', ['wall_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Post to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('mn_wall/*/');
        return $resultRedirect;
    }
}
