<?php

/**
 * REST Controller for User
 *
 * @author Mauricio Lauffer 
 */
class UserController extends Extra_RESTController
{
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_GET)) { return; }
        if (!$this->isValidSession()) { return; }
        return;

        $categoriasDao = new Application_Model_CategoriasDao();
        $contasDao = new Application_Model_ContasDao();
        $lancamentosDao = new Application_Model_LancamentosDao();
        $consumidoresDao = new Application_Model_ConsumidoresDao();

        try {
            $categorias = $categoriasDao->fetchAll($this->_consumidor->iduser);
            $contas = $contasDao->fetchAll($this->_consumidor->iduser);
            $lancamentos = $lancamentosDao->fetchAll($this->_consumidor->iduser);
            $consumidores = $consumidoresDao->fetchAll();

            $user = $consumidores->toArray();
            //$user[''] =


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
        //if (!$this->isValidSession()) { return; }

        //$this->convertPayloadToBackendFormat($this->getRequest()->getPost());
        $this->convertPayloadToBackendFormat(Zend_Json::decode($this->getRequest()->getRawBody()));

        $this->_form->removeElement('passwdold');

        if (!$this->isValidInput()) { return; }

        $consumidoresModel = new Application_Model_Consumidores($this->_form->getValues());
        $consumidoresDAO = new Application_Model_ConsumidoresDao();

        try {
            $consumidoresDAO->insert($consumidoresModel);
			$mail = new Extra_Mail();
			$mail->sendNewUser($this->_form->getValue('email'), $this->_form->getValue('passwd'));
            Extra_UserSession::createLoginSession( 'passwd', $this->_form->getValue('email'), $this->_form->getValue('passwd') );
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

        $consumidoresDAO = new Application_Model_ConsumidoresDao();
        $idconsumidor = $this->getRequest()->getParam('id', null);

        try {
            $consumidor = $consumidoresDAO->find($this->_consumidor->iduser, $idconsumidor);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }
        if (!$consumidor) {
            Extra_ErrorREST::setNotFound($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(200)->setBody(Zend_Json::encode($consumidor));
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

        $this->_form->removeElement('captcha');
        $this->_form->removeElement('tec');
        $oldPassword = $this->_form->getValue('passwdold');
        if (empty($oldPassword)) {
            $this->_form->removeElement('passwdold');
            $this->_form->removeElement('passwd');
            $this->_form->removeElement('passwdconf');
        } else {

        }



        if (!$this->isValidInput()) { return; }

        $consumidoresModel = new Application_Model_Consumidores($this->_form->getValues());
        $consumidoresDAO = new Application_Model_ConsumidoresDao();

        try {
            $consumidoresDAO->update($consumidoresModel);
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

        $idconsumidor = $this->getRequest()->getParam('id', null);
        $consumidoresDAO = new Application_Model_ConsumidoresDao();

        try {
            $consumidoresDAO->delete($this->_consumidor->iduser, $idconsumidor);
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
    private function convertPayloadToBackendFormat(array $payload)
    {
        $convertedPayload = $payload;
        //$convertedPayload['iduser'] = $this->_consumidor->iduser;
        $convertedPayload['iduser'] = ($this->getRequest()->isPost()) ? $payload['iduser'] : $this->_consumidor->iduser;

        $this->_form = new Application_Form_Consumidores();
        $this->_form->populate($convertedPayload);
    }
} //end CLASS
