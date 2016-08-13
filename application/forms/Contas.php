<?php

/**
 * Account Form
 *
 * @author Mauricio Lauffer 
 */
class Application_Form_Contas extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');
        $this->setName('conta');

        $this->addElement('hidden', 'iduser', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('hidden', 'idconta', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('text', 'idtipo', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('text', 'descricao', array(
            'required'  => true,
            'filters'   => array('StringTrim', 'StripTags'),
            'validators' => array(array('StringLength', false, array(1, 100)))
        ));

        $this->addElement('text', 'limitecredito', array(
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Float')));

        $this->addElement('text', 'diafatura', array(
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits',
                                  array('Between', false, array(1, 31)))
        ));

        $this->addElement('text', 'dataabertura', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Date')));

        // And finally add some CSRF protection
        /*$this->addElement('hash', 'csrf', array( //'ignore' => true,
            'salt' => 'Contas.php'));*/
    } //end FUNCTION
}
