<?php

/**
 * User Form
 *
 * @author Mauricio Lauffer 
 */
class Application_Form_Consumidores extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');
        $this->setName('user');

        $this->addElement('hidden', 'iduser', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('hidden', 'login', array(
            'filters'    => array('StringTrim', 'StripTags', 'StringToLower')));

        $this->addElement('text', 'email', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags', 'StringToLower'),
            'validators' => array('EmailAddress',
                                  array('StringLength', false, array(1, 100)))));

        $this->addElement('text', 'nome', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(array('StringLength', false, array(1, 100)))));

        $this->addElement('text', 'sexo', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags', 'StringToUpper'),
            'validators' => array('Alpha',
                                  array('InArray', false, array('F' => 'F', 'M' => 'M')))
        ));

        $this->addElement('text', 'datanascimento', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Date')));


        $this->addElement('checkbox', 'alert', array(
            'filters'    => array('StringTrim', 'StripTags', 'Boolean')));
        $this->addElement('checkbox', 'tec', array(
            'filters'    => array('StringTrim', 'StripTags', 'Boolean')));


        $this->addElement('password', 'passwdold', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(array('StringLength', false, array(1, 50)))));

        $this->addElement('password', 'passwd', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(array('StringLength', false, array(1, 50)))));

        $this->addElement('password', 'passwdconf', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(array('StringLength', false, array(1, 50)),
                                  array('Identical', false, array('token' => 'passwd')))
        ));




        /*// Add a captcha
        $this->addElement('captcha', 'captcha', array(
        //'label'      => 'Digite as 5 letras exibidas abaixo:' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/css/Codystar-Regular.ttf',
        'label'      => 'Digite as 5 letras exibidas abaixo:',
        'required'   => true,
        'captcha'    => array(
        'captcha'    => 'Word', //'Image',//'Dumb', //'Figlet',
        //'ImgAlt'     => 'Captcha',
        //'font'       => APPLICATION_PATH . '/../public/verdana.ttf',//'/../data/fonts/verdana.ttf',
        //'imgDir'     => Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/js/',
        //'imgUrl'     => Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/css/',
        //'fontsize'   => 16,
        //'width'      => 200,
        //'height'     => 60,
        //'dotNoiseLevel' => 30,
        'wordLen'    => 6,
        'timeout'    => 300000,
        //'lineNoiseLevel' => 3
        )
        ));*/


        $captcha = $this->createElement('captcha', 'captcha', array('required' => true,
                                                                    'captcha' => array(
                                                                        'captcha' => 'Image',
                                                                        //'font' => 'C:/Windows/Fonts/arial.ttf',
                                                                        'font' => APPLICATION_PATH . '/../public/fonts/arial.ttf',
                                                                        'fontSize' => '30',
                                                                        'wordLen' => 5,
                                                                        'height' => '80',
                                                                        'width' => '150',
                                                                        'imgDir' => APPLICATION_PATH . '/../public/captcha',
                                                                        'imgUrl' => Zend_Controller_Front::getInstance()->getBaseUrl() . '/captcha',
                                                                        'dotNoiseLevel' => 40,
                                                                        'lineNoiseLevel' => 4)));

        //$this->addElement($captcha);


        // And finally add some CSRF protection
        /*$this->addElement('hash', 'csrf', array( //'ignore' => true,
            'salt' => 'Contas.php'));*/

    } //end FUNCTION
}
