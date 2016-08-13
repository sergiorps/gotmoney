<?php
class Extra_Mail
{
    private $_appConfig;

    /**
     * Return an associative array of the stored data.
     *
     * @return object
     */
    private function getTransport()
    {
        $appConfig = $this->getAppConfig();
        $config = array('ssl' => $appConfig->params->ssl,
                        'port' => $appConfig->params->port,
                        'auth' => $appConfig->params->auth,
                        'username' => $appConfig->params->username,
                        'password' => $appConfig->params->password
        );
        /*$config = array('ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ));*/
        return new Zend_Mail_Transport_Smtp($appConfig->smtp, $config);
    } //end FUNCTION



    /**
     * Return an associative array of the stored data.
     *
     * @return object
     */
    private function getMailObject($emailTo, $subject, $text)
    {
        $appConfig = $this->getAppConfig();
        $mail = new Zend_Mail($appConfig->params->encoding);
        $mail->setFrom($appConfig->params->senderMail, $appConfig->params->senderName);
        $mail->addTo($emailTo);
        $mail->setSubject($subject);
        $mail->setBodyText($text);
        $mail->setBodyHtml($text);
        return $mail;
    } //end FUNCTION


    /**
     * Return an associative array of the stored data.
     *
     * @return object
     */
    private function getAppConfig()
    {
        if (empty($this->_appConfig)) {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
            $this->_appConfig = $config->resources->mail;
        }
        return $this->_appConfig;
    } //end FUNCTION


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function sendRecoverPassword($email, $password)
    {
        $subject = 'GotMoney App - password recovery';
        $text = 'Voc&ecirc; solicitou uma nova senha. Seu novo password para a conta <b>' . $email . '</b> &eacute;: <b>' . $password . '</b>';
        $mail = $this->getMailObject($email, $subject, $text);
        $mail->send();
    } //end FUNCTION
    
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function sendNewUser($email, $password)
    {
        $subject = 'GotMoney App - new user';
        $text = 'Voc&ecirc; criou uma conta no site GotMoney App. Seu usu&aacute;rio/senha de acesso &eacute;: <b>' . $email . '</b> / <b>' . $password . '</b>';
        $mail = $this->getMailObject($email, $subject, $text);
        //$mail->send($this->getTransport());
        $mail->send();
    } //end FUNCTION

} //end CLASS
