<?php

/**
 * Lancamentos Form
 *
 * @author Mauricio Lauffer 
 */
class Application_Form_Lancamentos extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('accept-charset', 'UTF-8');
        $this->setName('lancamentos');

        $this->addElement('hidden', 'iduser', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('hidden', 'idlancamento', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('hidden', 'idlancamentopai', array(
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('text', 'idconta', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Digits')));

        $this->addElement('text', 'tipo', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags', 'StringToUpper'),
            'validators' => array('Alpha',
                                  array('InArray', false, array('D' => 'D', 'C' => 'C')))
        ));
        /*'MultiOptions' => array('D' => '(-) Despesa',
                                'C' => '(+) Receita')*/

        $this->addElement('text', 'descricao', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array(array('StringLength', false, array(1, 100)))));

        $this->addElement('text', 'valor', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Float')));

        $this->addElement('text', 'recorrencia', array(
            'filters'    => array('StringTrim', 'StripTags', 'StringToUpper'),
            'validators' => array('Alpha',
                                  array('InArray', false, array('U' => 'U',
                                                                'D' => 'D',
                                                                'W' => 'W',
                                                                'M' => 'M',
                                                                'Y' => 'Y')))
        ));
        /*'MultiOptions' => array(
            'U' => 'Único',
            'D' => 'Diário',
            'W' => 'Semanalmente',
            'M' => 'Mensalmente',
            'Y' => 'Anualmente')*/

        $this->addElement('text', 'datavencimento', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Date')));

        $this->addElement('text', 'datalancamento', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Date')));

        $this->addElement('text', 'datainicio', array(
            'filters'    => array('StringTrim', 'StripTags'),
            'validators' => array('Date')));

        $this->addElement('text', 'parcela', array(
            'maxlength' => 50,
            'filters'   => array('StringTrim', 'StripTags')));
        //$this->datainicio->setIgnore(true);

        $this->addElement('checkbox', 'idstatus', array(
            'filters'    => array('StringTrim', 'StripTags')));
        $this->addElement('checkbox', 'editrecurrency', array(
            'filters'    => array('StringTrim', 'StripTags', 'Boolean')));

        $this->addElement('text', 'tag', array(
            'filters'    => array('StringTrim', 'StripTags')));

        $this->addElement('text', 'origem', array(
            'required'   => true,
            'filters'    => array('StringTrim', 'StripTags', 'StringToUpper'),
            'validators' => array('Alpha',
                array('InArray', false, array('W' => 'W', 'M' => 'M')))
        ));
        /*'MultiOptions' => array('W' => 'Web',
                                'M' => 'Mobile')*/


        /*$this->addElement('text', 'tags', array(
            'label' => 'Tags de classificação (separadas por ";")',
            'placeholder' => 'tags separadas por ";". Ex: roupa; comida; aluguel...',
            'size' => 200,
            'maxlength' => 200,
            'filters' => array('StringTrim', 'StripTags')));*/


        /*$newBox = array();
        $categoriasDao = new Application_Model_CategoriasDao();
        $categorias = $categoriasDao->fetchAll( $this->_consumidor->iduser );
        foreach ($categorias as $categoria) {
            $this->addElement('checkbox', 'idcategoria' . $categoria->getIdcategoria(), array(
                'label' => $categoria->getDescricao(),
                //'value' => 1
            ));
            $newBox[] = 'idcategoria' . $categoria->getIdcategoria();
        }*/

        /*// And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array( //'ignore' => true,
                'salt' => 'Lancamentos.php'));*/
    } //end FUNCTION
}
