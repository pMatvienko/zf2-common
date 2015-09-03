<?php
namespace SbxCommon\Crud;

use SbxCommon\Form\FooterFieldset;
use Zend\Form\Form as ZendForm;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractForm extends ZendForm implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    const BTN_SAVE = 'save';
    const BTN_CONTINUE = 'continue';
    const BTN_CANCEL = 'cancel';
    const FOOTER_NAME = 'footer';

    public function __construct($name = null, $options = array())
    {
        if(!empty($options['serviceLocator']) && $options['serviceLocator'] instanceof ServiceLocatorInterface){

            $this->setServiceLocator($options['serviceLocator']);
        }
        parent::__construct($name, $options);
        $this->setUp();
        $this->add($this->getFooter());
    }

    abstract protected function setUp();

    public function getFooter()
    {
        $fieldset = new FooterFieldset(self::FOOTER_NAME);
        $fieldset
            ->add(array(
                'name' => self::BTN_SAVE,
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'action:save',
                    'class' => 'btn btn-success'
                ),
                'options' => array(
                    'label' => 'action:save',
                ),
            ))
            ->add(array(
                'name' => self::BTN_CONTINUE,
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'action:save-continue',
                    'class' => 'btn btn-primary'
                ),
                'options' => array(
                    'label' => 'action:save',
                ),
            ))
            ->add(array(
                'name' => self::BTN_CANCEL,
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'action:cancel',
                    'class' => 'btn btn-warning'
                ),
                'options' => array(
                    'label' => 'action:cancel',
                ),
            ));
        return $fieldset;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }
}