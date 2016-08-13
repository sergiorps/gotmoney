<?php

/**
 * REST Controller for Category
 *
 * @author Mauricio Lauffer 
 */
class CategoryController extends Extra_RESTController
{
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        //print_r($_SERVER);
        //print_r($_COOKIE);

        if (!$this->isValidHttpMethod(self::HTTP_METHOD_GET)) { return; }
        if (!$this->isValidSession()) { return; }

        $categoriasDao = new Application_Model_CategoriasDao();

        try {
            $categorias = $categoriasDao->fetchAll($this->_consumidor->iduser);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(200)->setBody(Zend_Json::encode( $categorias ));
    }



    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction() //Create
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_POST)) { return; }
        if (!$this->isValidSession()) { return; }

        //$this->convertPayloadToBackendFormat($this->getRequest()->getPost());
        $this->convertPayloadToBackendFormat(Zend_Json::decode($this->getRequest()->getRawBody()));

        if (!$this->isValidInput()) { return; }

        $categoriasModel = new Application_Model_Categorias($this->_form->getValues());
        $categoriasDAO = new Application_Model_CategoriasDao();

        try {
            $categoriasDAO->insert($categoriasModel);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(201)->setBody(Zend_Json::encode(''));
    }



    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction() //Read
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_GET)) { return; }
        if (!$this->isValidSession()) { return; }

        $categoriasDAO = new Application_Model_CategoriasDao();
        $idcategoria = $this->getRequest()->getParam('id', null);

        try {
            $categoria = $categoriasDAO->find($this->_consumidor->iduser, $idcategoria);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }
        if (!$categoria) {
            Extra_ErrorREST::setNotFound($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(200)->setBody(Zend_Json::encode($categoria));
    }



    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction() //Update
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_PUT)) { return; }
        if (!$this->isValidSession()) { return; }

        $this->convertPayloadToBackendFormat($this->getRequest()->getParams());

        if (!$this->isValidInput()) { return; }

        $categoriasModel = new Application_Model_Categorias($this->_form->getValues());
        $categoriasDAO = new Application_Model_CategoriasDao();

        try {
            $result = $categoriasDAO->update($categoriasModel);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }
        /*if ($result === 0) {
        	Extra_ErrorREST::setNothingChanged($this->getResponse());
        	return;
        }*/

        $this->getResponse()->setHttpResponseCode(204)->setBody(null);
    }



    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction() //Delete
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_DELETE)) { return; }
        if (!$this->isValidSession()) { return; }

        $idcategoria = $this->getRequest()->getParam('id', null);
        $categoriasDAO = new Application_Model_CategoriasDao();

        try {
            $categoriasDAO->delete($this->_consumidor->iduser, $idcategoria);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(204)->setBody(null);
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    protected function convertPayloadToBackendFormat(array $payload)
    {
        $convertedPayload = $payload;
        $convertedPayload['iduser'] = $this->_consumidor->iduser;

        $this->_form = new Application_Form_Categorias();
        $this->_form->populate($convertedPayload);
    }
} //end CLASS
