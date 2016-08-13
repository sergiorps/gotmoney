<?php

/**
 * REST Controller for Account
 *
 * @author Mauricio Lauffer 
 */
class AccountController extends Extra_RESTController
{
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_GET)) { return; }
        if (!$this->isValidSession()) { return; }

        $contasDao = new Application_Model_ContasDao();

        try {
            $contas = $contasDao->fetchAll($this->_consumidor->iduser);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(200)->setBody(Zend_Json::encode( $contas ));
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

        $contasModel = new Application_Model_Contas($this->_form->getValues());
        $contasDAO = new Application_Model_ContasDao();

        try {
            $contasDAO->insert($contasModel);
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

        $contasDAO = new Application_Model_ContasDao();
        $idconta = $this->getRequest()->getParam('id', null);

        try {
            $conta = $contasDAO->find($this->_consumidor->iduser, $idconta);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }
        if (!$conta) {
            Extra_ErrorREST::setNotFound($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(200)->setBody(Zend_Json::encode($conta));
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

        $contasModel = new Application_Model_Contas($this->_form->getValues());
        $contasDAO = new Application_Model_ContasDao();

        try {
            $contasDAO->update($contasModel);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }

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

        $idconta = $this->getRequest()->getParam('id', null);
        $contasDAO = new Application_Model_ContasDao();

        try {
            $contasDAO->delete($this->_consumidor->iduser, $idconta);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(204)->setBody(null);
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return boolean
     */
    protected function convertPayloadToBackendFormat(array $payload)
    {
        $convertedPayload = $payload;
        $convertedPayload['iduser'] = $this->_consumidor->iduser;
        $convertedPayload['limitecredito'] = (empty($payload['limitecredito'])) ? 0 : $payload['limitecredito'];
        $convertedPayload['diafatura'] = (empty($payload['diafatura'])) ? 1 : $payload['diafatura'];

        $this->_form = new Application_Form_Contas();
        $this->_form->populate($convertedPayload);
    }
} //end CLASS
