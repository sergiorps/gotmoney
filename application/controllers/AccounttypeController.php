<?php

/**
 * REST Controller for Account Type
 *
 * @author Mauricio Lauffer 
 */
class AccounttypeController extends Extra_RESTController
{
    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        if (!$this->isValidHttpMethod(self::HTTP_METHOD_GET)) { return; }
        if (!$this->isValidSession()) { return; }

        $contastiposDao = new Application_Model_ContastiposDao();

        try {
            $contastipos = $contastiposDao->fetchAll();
        } catch (Zend_Exception $e) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return;
        }

        $this->getResponse()->setHttpResponseCode(200)->setBody(Zend_Json::encode( $contastipos ));
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

        $this->_form = new Application_Form_Contas();
        $this->_form->populate($convertedPayload);
    }
} //end CLASS
