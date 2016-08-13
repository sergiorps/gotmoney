<?php

/**
 * REST Controller default actions
 *
 * @author Mauricio Lauffer 
 */
abstract class Extra_RESTController extends Zend_Rest_Controller
{
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_PUT    = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';

    protected $_consumidor;
    protected $_form;


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8');
    } //end FUNCTION



    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        Extra_ErrorREST::setInvalidHTTPMethod($this->getResponse());
    }



    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        Extra_ErrorREST::setInvalidHTTPMethod($this->getResponse());
    }



    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        Extra_ErrorREST::setInvalidHTTPMethod($this->getResponse());
    }



    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        Extra_ErrorREST::setInvalidHTTPMethod($this->getResponse());
    }



    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        Extra_ErrorREST::setInvalidHTTPMethod($this->getResponse());
    }



    /**
    * The head action handles HEAD requests; it should respond with an
    * identical response to the one that would correspond to a GET request,
    * but without the response body.
    */
    public function headAction()
    {
        Extra_ErrorREST::setInvalidHTTPMethod($this->getResponse());
    }



    /**
     * The options action handles OPTIONS requests; it should respond with
     * the HTTP methods that the server supports for specified URL.
     */
    public function optionsAction()
    {
        Extra_ErrorREST::setInvalidHTTPMethod($this->getResponse());
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return boolean
     */
    protected function isValidOrigin()
    {
        //TODO
        //Check origem
        $domain = 'wamp';
        //$domain = 'gotmoneyapp.com';
        $serverHost = parse_url('http://' . $this->getRequest()->getServer('SERVER_NAME'));
        $httpHost = parse_url('http://' . $this->getRequest()->getServer('HTTP_HOST'));
        $httpOrigin = parse_url($this->getRequest()->getServer('HTTP_ORIGIN'));
        $httpReferer = parse_url($this->getRequest()->getServer('HTTP_REFERER'));

        if (!empty($httpOrigin['host']) && $httpOrigin['host'] != $httpHost['host']) {
            return false;
            //exit("CSRF protection in POST request - detected invalid Origin header: " . htmlspecialchars($_SERVER["HTTP_ORIGIN"]));
        } elseif (!empty($httpReferer['host']) && $httpReferer['host'] != $httpHost['host']) {
            return false;
            //exit($httpHost . "CSRF protection in POST request - detected invalid Referer header: " . htmlspecialchars($_SERVER["HTTP_REFERER"]));
        } else {
            //return false;
            //exit("CSRF protection in POST request - NO HEADER");
        }

        if ($domain === $httpHost['host']) {
            return false;
        }

        return true;
    }


    /**
     * Return an associative array of the stored data.
     *
     * @return boolean
     */
    protected function isValidSession()
    {
        //Login check
        $this->_consumidor = Extra_UserSession::getUserLogged();
        if ($this->_consumidor === null) {
            Extra_ErrorREST::setUserNotLoggedIn($this->getResponse());
            return false;
        }
        return true;
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return boolean
     */
    protected function isValidHttpMethod($httpMethod)
    {
        $isValid = true;
        switch ($httpMethod) {
            case self::HTTP_METHOD_GET:
                if (!$this->getRequest()->isGet()) { $isValid = false; }                
                break;

            case self::HTTP_METHOD_POST:
                if (!$this->getRequest()->isPost()) { $isValid = false; } 
                break;

            case self::HTTP_METHOD_PUT:
                if (!$this->getRequest()->isPut()) { $isValid = false; } 
                break;

            case self::HTTP_METHOD_DELETE:
                if (!$this->getRequest()->isDelete()) { $isValid = false; } 
                break;

            default:
                $isValid = false;
                break;
        }

        //Check if is AJAX request
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $isValid = false;
        }

        if (!$this->isValidOrigin()) {
            //TODO
            //$isValid = false;
        }

        if (!$isValid) {
            Extra_ErrorREST::setInvalidHTTPMethod($this->getResponse());
        }

        return $isValid;
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return boolean
     */
    protected function isValidInput()
    {
        if (empty($this->_form)) {
            Extra_ErrorREST::setInternalServerError($this->getResponse());
            return false;
        }
        if (!$this->_form->isValid($this->_form->getValues())) {
            Extra_ErrorREST::setInvalidInput($this->getResponse(), $this->_form->getMessages());
            return false;
        }
        return true;
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    protected function convertPayloadToBackendFormatMODEL(array $payload, $formClass)
    {
        $convertedPayload = $payload;

        if (!empty($this->_consumidor->iduser)) {
            $convertedPayload['iduser'] = $this->_consumidor->iduser;
        }

        $this->_form = new $formClass();
        $this->_form->populate($convertedPayload);
    }
} //end CLASS
