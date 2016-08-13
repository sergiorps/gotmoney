<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Setup the application session.
     *
     * @return void
     * @link http://framework.zend.com/manual/en/zend.session.html
     */
    /*public function xxx_initSession()
    {
        //TODO
        $options = $this->getOptions();
        $sessionOptions = array(
            'save_path' => $options['resources']['session']['save_path']
        );
        Zend_Session::setOptions($options);
        Zend_Session::start();
        //Zend_Session::regenerateId();


        $this->bootstrap('db');

        $session = $this->getPluginResource('session');
        $session->init();

        Zend_Session::start($this->getOptions());

        $defaultNamespace = new Zend_Session_Namespace();

        if (!isset($defaultNamespace->initialized))
        {
            Zend_Session::regenerateId();
            //Zend_Session::registerValidator(new Biblio_Session_Validator_Ip());
            Zend_Session::registerValidator(new Zend_Session_Validator_HttpUserAgent());
            $defaultNamespace->initialized = true;
            $defaultNamespace->time = time();
            $defaultNamespace->role = 'guest';
        }
    }*/


    /**
     * Setup the application cache.
     *
     * @return Zend_Cache
     * @link http://framework.zend.com/manual/en/zend.cache.html
     */
    protected function xxx_initCache()
    {
        //TODO
        /*
resources.cachemanager.database.frontend.name = Core
resources.cachemanager.database.frontend.customFrontendNaming = false
resources.cachemanager.database.frontend.options.lifetime = 7200
resources.cachemanager.database.frontend.options.automatic_serialization = true
resources.cachemanager.database.backend.name = File
resources.cachemanager.database.backend.customBackendNaming = false
resources.cachemanager.database.backend.options.cache_dir = "/path/to/cache"
resources.cachemanager.database.frontendBackendAutoload = false
         */

        /*$this->bootstrap('Config');
        $appConfig = $this->getOptions();
        $cache = null;

        // Only attempt to init the cache if turned on
        if ($appConfig->app->caching) {
            // Get the cache settings
            $config = $appConfig->app->cache;
            try {
                $cache = Zend_Cache::factory(
                    $config->frontend->adapter,
                    $config->backend->adapter,
                    $config->frontend->options->toArray(),
                    $config->backend->options->toArray() );

                // Set the cache to be used with all table objects
                //Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
            }
            catch (Zend_Cache_Exception $e) {
                // ...
            }
            //Zend_Registry::set('cache', $cache);
            //Zend_Locale_Data::setCache($cache);

            //return $cache;
        }*/
    }


    /**
     * Initializes the logging on the application
     *
     * We're using 2 ways for logging exceptions:
     * 1. PHP Error log file.
     * 2. Firebug for Development environments only.
     *
     * @return Zend_Log
     * @link http://framework.zend.com/manual/en/zend.cache.html
     */
    protected function _initLogger()
    {
        $this->bootstrap("log");
        $logger = $this->getResource('log');
        Zend_Registry::set("logger", $logger);
        return $logger;
    }


    /**
     * Setup the application REST Route
     *
     * @return Zend_Rest_Route
     * @link http://framework.zend.com/manual/1.12/en/zend.controller.router.html#zend.controller.router.routes.rest
     */
    protected function _initRoutes()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($frontController);
        $frontController->getRouter()->addRoute('default', $restRoute);
        $frontController->registerPlugin(new Zend_Controller_Plugin_PutHandler());
    }


    /**
     * Setup the application REST Route
     *
     * @return Zend_Rest_Route
     * @link http://framework.zend.com/manual/1.12/en/zend.controller.router.html#zend.controller.router.routes.rest
     */
    protected function _initRestRoute()
    {
        /*$this->bootstrap('frontController');
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($frontController);
        $frontController->getRouter()->addRoute('default', $restRoute);
        $frontController->registerPlugin(new Zend_Controller_Plugin_PutHandler());*/
    }
}