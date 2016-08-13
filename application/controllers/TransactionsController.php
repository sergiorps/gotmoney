<?php

/**
 * REST Controller for Transactions
 *
 * @author Mauricio Lauffer 
 */
class TransactionsController extends Extra_RESTController
{
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_GET)) { return; }
        if (!$this->isValidSession()) { return; }

        $lancamentosDao = new Application_Model_LancamentosDao();

        try {
            $lancamentos = $lancamentosDao->fetchAll($this->_consumidor->iduser);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(200)->setBody(Zend_Json::encode( $lancamentos ));
    }



    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction() //Create
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_POST)) { return; }
        if (!$this->isValidSession()) { return; }

        //$payload = $this->getRequest()->getPost();
        $payload = Zend_Json::decode($this->getRequest()->getRawBody());

        foreach($payload['data'] as $item) {
            $this->convertPayloadToBackendFormat($item);

            if (!$this->isValidInput()) { return; }

            $lancamentosModel = new Application_Model_Lancamentos($this->_form->getValues());
            $lancamentosDAO = new Application_Model_LancamentosDao();

            try {
                $lancamentosDAO->insert($lancamentosModel);
            } catch (Zend_Exception $e) {
                Extra_ErrorREST::setInternalServerError($this->getResponse());
                return;
            }
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

        $lancamentosDAO = new Application_Model_LancamentosDao();
        $idlancamento = $this->getRequest()->getParam('id', null);

        try {
            $lancamento = $lancamentosDAO->find($this->_consumidor->iduser, $idlancamento);
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }
        if (!$lancamento) {
            Extra_ErrorREST::setNotFound($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(200)->setBody(Zend_Json::encode($lancamento));
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

        $lancamentosModel = new Application_Model_Lancamentos($this->_form->getValues());
        $lancamentosDAO = new Application_Model_LancamentosDao();

        try {
            //Verifica se edita recorrência
            $editRecurrency = null; //$this->_form->getValue('editrecurrency');
            //if (empty($this->_form->getValue('editrecurrency'))) {
            if (empty($editRecurrency)) {
                $lancamentosDAO->update($lancamentosModel);
            } else {
                $lancamentosDAO->editRecurrency($lancamentosModel);
            }

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

        $idlancamento = $this->getRequest()->getParam('id', null);
        $lancamentosDAO = new Application_Model_LancamentosDao();

        try {
            $lancamentosDAO->delete($this->_consumidor->iduser, $idlancamento);
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
        unset($convertedPayload['tag']);
        //$convertedPayload['idstatus'] = ($payload['idstatus']) ? '001' : '000';

        if (!empty($payload['tag'])) {
            for ($i = 0; $i < count($payload['tag']); $i++) {
                if (empty($payload['tag'][$i])) {
                    unset($payload['tag'][$i]);
                }
            }
            $convertedPayload['tag'] = implode(', #', $payload['tag']);
            $convertedPayload['tag'] = '#' . $convertedPayload['tag'];
        }

        $this->_form = new Application_Form_Lancamentos();
        $this->_form->populate($convertedPayload);
    }
} //end CLASS
