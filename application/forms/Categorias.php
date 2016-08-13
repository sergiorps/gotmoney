<?php

/**
 * Category Form
 *
 * @author Mauricio Lauffer 
 */
class Application_Form_Categorias extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');
        $this->setName('categoria');

        $this->addElement('hidden', 'iduser', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('hidden', 'idcategoria', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('text', 'descricao', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'), //, 'HtmlEntities'
            'validators' => array(array('StringLength', false, array(1, 100)))));

        // And finally add some CSRF protection
        /*$this->addElement('hash', 'csrf', array( //'ignore' => true,
                'salt' => 'Categorias.php'));*/
    } //end FUNCTION
}
