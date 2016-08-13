<?php

class SessionController extends Extra_RESTController
{
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function postAction()
    {
		Zend_Auth::getInstance()->clearIdentity();
		if (!$this->isValidHttpMethod(self::HTTP_METHOD_POST)) { return; }
		//$this->convertPayloadToBackendFormat($this->getRequest()->getPost());
		$this->convertPayloadToBackendFormat(Zend_Json::decode($this->getRequest()->getRawBody()));
		$usersession = false;

		//Login check
		try {
			switch ($this->_form->getValue('login')) {
				case 'system':
					$this->_form->removeElement('iduser');
					$this->_form->removeElement('nome');
					$this->_form->removeElement('token');
					if (!$this->isValidInput()) { return; }
					$usersession = $this->loginSystem();
					break;

				case 'facebook':
					$this->_form->removeElement('passwd');
					if (!$this->isValidInput()) { return; }
					$usersession = $this->loginFacebook();
					break;

				case 'google':
					$this->_form->removeElement('passwd');
					if (!$this->isValidInput()) { return; }
					$usersession = $this->loginGoogle();
					break;

				default:
					$usersession = false;
					break;
			}

		} catch (Exception $e) {
			Extra_ErrorREST::setInternalServerError($this->getResponse());
		}

		if ( $usersession ) {
			$this->getResponse()->setHttpResponseCode(200)->appendBody(Zend_Json::encode(''));
		} else {
			Extra_ErrorREST::setInvalidUserPassword($this->getResponse());
		}
    }

    
    
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getAction()
    {
		if (!$this->isValidHttpMethod(self::HTTP_METHOD_GET)) { return; }

		$auth = Zend_Auth::getInstance();
		//Verifica se login foi efetuado
		if ($auth->hasIdentity()) {
			$user = $auth->getIdentity();
			$this->getResponse()->setHttpResponseCode(200)->appendBody(Zend_Json::encode(''));
		} else {
			Extra_ErrorREST::setUserNotLoggedIn($this->getResponse());
		}
	}
        
    
    
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function deleteAction()
    {
    	if (!$this->isValidHttpMethod(self::HTTP_METHOD_DELETE)) { return; }

    	try {
    		//Atualiza saldo de todas as contas do user
    		$contasDao = new Application_Model_ContasDao();
    		//$contasDao->setBalance($this->_consumidor->iduser);
    	} catch (Exception $e) {
			$logger = Zend_Registry::get('logger');
			$logger->err($e);
    	}
    	
    	Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy(true, true);
    	$this->getResponse()->setHttpResponseCode(200)->appendBody(Zend_Json::encode(''));
    }


	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	public function putAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::regenerateId();
		if (!$this->isValidHttpMethod(self::HTTP_METHOD_PUT)) { return; }

		$this->convertPayloadToBackendFormat($this->getRequest()->getParams());

		$this->_form->removeElement('iduser');
		$this->_form->removeElement('nome');
		$this->_form->removeElement('passwd');
		$this->_form->removeElement('token');
		$this->_form->removeElement('login');

		if (!$this->isValidInput()) { return; }

		$consumidorDao = new Application_Model_ConsumidoresDao();
		$consumidorResult = $consumidorDao->findEmail( $this->_form->getValue('email') );
		if (empty($consumidorResult)) {
			Extra_ErrorREST::setEmailNotFound($this->getResponse());
			return;
		}

		$consumidor = new Application_Model_Consumidores($consumidorResult->toArray());

		//Update password
		$newPassword = sha1( md5($consumidor->getEmail()) . md5(uniqid(mt_rand(), true)) );
		$consumidor->setPasswd( $newPassword );
		$consumidorDao->update( $consumidor );
		//Send email
		try {
			$mail = new Extra_Mail();
			$mail->sendRecoverPassword($consumidor->getEmail(), $newPassword);
		} catch (Exception $e) {

			$this->getResponse()->setHttpResponseCode(500)->setBody(Zend_Json::encode($e->getMessage()));
			//Extra_ErrorREST::setInternalServerError($this->getResponse());
			return;
		}
		$this->getResponse()->setHttpResponseCode(200)->appendBody(Zend_Json::encode(''));
	}


	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	private function convertPayloadToBackendFormat(array $payload)
	{
		$convertedPayload = $payload;

		if (empty($payload['sexo'])) {
			$convertedPayload['sexo'] = 'M';
		} else {
			$convertedPayload['sexo'] = strtoupper(substr($payload['sexo'],0,1));
		}

		if (empty($payload['datanascimento'])) {
			$convertedPayload['datanascimento'] = "2000-01-01";
		}

		$this->_form = new Application_Form_Login();
		$this->_form->populate($convertedPayload);
	}


	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	public function loginSystem() {
		$email = $this->_form->getValue('email');
		$passwd = $this->_form->getValue('passwd');
		return Extra_UserSession::createLoginSession( 'passwd', $email, $passwd );
	}


	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	private function loginFacebook() {
		return Extra_LoginFacebook::login($this->_form->getValues());
	}


	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	private function loginGoogle()
	{
		return Extra_LoginGoogle::login($this->_form->getValues());
	}
} //end CLASS
