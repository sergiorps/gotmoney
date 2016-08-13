<?php

/**
 * Login Form
 *
 * @author Mauricio Lauffer 
 */
class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');
        $this->setName('login');

        $this->addElement('hidden', 'iduser', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('text', 'login', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Alpha',
                array('InArray', false, array(
                    'facebook' => 'facebook',
                    'google' => 'google',
                    'system' => 'system')))
        ));

        $this->addElement('text', 'email', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags', 'StringToLower'),
            'validators' => array('EmailAddress', array('StringLength', false, array(1, 80)))));

        $this->addElement('text', 'nome', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(array('StringLength', false, array(1, 80)))));

        $this->addElement('text', 'sexo', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags', 'StringToUpper'),
            'validators' => array('Alpha',
                                  array('InArray', false, array('F' => 'F', 'M' => 'M')))
        ));

        $this->addElement('text', 'datanascimento', array(
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Date')));

        $this->addElement('password', 'passwd', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(array('StringLength', false, array(1, 80)))));

        $this->addElement('text', 'token', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags')));


        // And finally add some CSRF protection
        //$this->addElement('hash', 'csrf', array('salt' => md5(uniqid(rand(), TRUE))));
    } //end FUNCTION
}
