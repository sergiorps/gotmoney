<?php
class Extra_ErrorREST
{

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function setNotFound($response)
    {
        $aError = array('error'       => true,
                        'message'     => 'Not found!',
                        'messageCode' => 'Error.notFound');
        $response->setHttpResponseCode(400)->setBody(Zend_Json::encode($aError));
        $logger = Zend_Registry::get('logger');
        $logger->err($aError['message']);
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function setUserNotLoggedIn($response)
    {
        $aError = array('error'       => true,
                        'message'     => 'User is not logged in the system!',
                        'messageCode' => 'Error.userNotLoggedIn');
        $response->setHttpResponseCode(401)->setBody(Zend_Json::encode($aError));
        $logger = Zend_Registry::get('logger');
        $logger->err($aError['message']);
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function setInvalidHTTPMethod($response)
    {
        $aError = array('error'       => true,
                        'message'     => 'HTTP Method is invalid!',
                        'messageCode' => 'Error.invalidHTTPMethod');
        $response->setHttpResponseCode(405)->setBody(Zend_Json::encode($aError));
        $logger = Zend_Registry::get('logger');
        $logger->err($aError['message']);
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function setNothingChanged($response)
    {
        $aError = array('error'       => true,
                        'message'     => 'Nothing has changed!',
                        'messageCode' => 'Error.nothingChanged');
        $response->setHttpResponseCode(500)->setBody(Zend_Json::encode($aError));
        $logger = Zend_Registry::get('logger');
        $logger->err($aError['message']);
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function setInternalServerError($response)
    {
        $aError = array('error'       => true,
                        'message'     => 'Internal Server Error!',
                        'messageCode' => 'Error.internalServerError');
        $response->setHttpResponseCode(500)->setBody(Zend_Json::encode($aError));
        $logger = Zend_Registry::get('logger');
        $logger->err($aError['message']);
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function setInvalidInput($response, $messages)
    {
        $aError = array('error'       => true,
                        'message'     => $messages,
                        'messageCode' => 'Error.invalidInput');
        $response->setHttpResponseCode(400)->setBody(Zend_Json::encode($aError));
        $logger = Zend_Registry::get('logger');
        $logger->err($aError['message']);
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function setEmailNotFound($response)
    {
        $error = self::getErrorMessage('Email not found!', 'Error.emailNotFound');
        $response->setHttpResponseCode(400)->setBody($error);
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function setInvalidUserPassword($response)
    {
        $error = self::getErrorMessage('Invalid user/password!', 'Error.invalidUserPassword');
        $response->setHttpResponseCode(404)->setBody($error);
    }


    private static function getErrorMessage($message, $messageCode) {
        $aError = array(
            'error'       => true,
            'message'     => $message,
            'messageCode' => $messageCode);

        $logger = Zend_Registry::get('logger');
        $logger->err($aError['message']);

        return Zend_Json::encode($aError);
    }
}
