<?php
namespace SbxCommon\Crud;

use SbxCommon\Base\AbstractController as BaseController;


abstract class AbstractController extends BaseController
{
    public function removeAction()
    {
        $model = $this->getModelToProcess();
        $entity = $model->findById($this->params()->fromRoute('id'));
        $model->removeEntity($entity);
        $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->flush();
        $this->flashMessenger()->addSuccessMessage('msg:record-removed');
        return $this->getAfterActionRedirect();
    }

    public function createAction()
    {
        $form = $this->getModelToProcess()->getForm();

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (!empty($post->footer[AbstractForm::BTN_CANCEL])) {
                return $this->getAfterActionRedirect();
            }
            $form->setData($post);
            if ($form->isValid()) {
                $this->getModelToProcess()->saveForm($form);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->flush();
                $this->flashMessenger()->addSuccessMessage('msg:record-saved');
                if (!empty($post->footer[AbstractForm::BTN_CONTINUE])) {
                    return $this->getAfterActionRedirect(array('action' => 'create'));
                } else {
                    return $this->getAfterActionRedirect();
                }
            }
        }

        return array(
            'form' => $form,
            'caption' => $this->getCaption()
        );
    }

    public function editAction()
    {
        $model = $this->getModelToProcess();
        /**
         * @var \System\Form\AclRole $form
         */
        $form = $model->getForm($model->findById($this->params()->fromRoute('id')));
        if($form->get(AbstractForm::FOOTER_NAME) != null) {
            $form->get(AbstractForm::FOOTER_NAME)->remove(AbstractForm::BTN_CONTINUE);
        }


        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if (!empty($post->footer[AbstractForm::BTN_CANCEL])) {
                return $this->redirect()->toRoute('system/default', array('action' => 'index'), true);
            }
            $form->setData($post);
            if ($form->isValid()) {
                $model->saveForm($form);
                $this->getServiceLocator()->get('Doctrine\ORM\EntityManager')->flush();
                $this->flashMessenger()->addSuccessMessage('msg:record-saved');
                return $this->getAfterActionRedirect();
            }
        }

        return array(
            'form' => $form,
            'caption' => $this->getCaption()
        );
    }

    public function indexAction()
    {
        $grid = $this->getModelToProcess()->getGrid();
        return array(
            'grid' => $grid,
            'caption' => $this->getCaption()
        );
    }

    /**
     * @return \System\Base\CrudModelInterface
     */
    abstract public function getModel();

    /**
     * @return CrudModelInterface
     * @throws \SbxCommon\Exception
     */
    protected function getModelToProcess()
    {
        $model = $this->getModel();
        if (!($model instanceof CrudModelInterface)) {
            throw new \SbxCommon\Exception('Provided model should be instance of "CrudModelInterface"');
        }
        return $model;
    }
}