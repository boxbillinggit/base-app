<?php

/**
 * Backend Index Controller
 * 
 * @package     base-app
 * @category    Controller
 * @version     1.2
 */

namespace Baseapp\Backend\Controllers;

use \Baseapp\Library\Auth,
    \Baseapp\Library\I18n;

class IndexController extends \Phalcon\Mvc\Controller
{

    /**
     * Initialize
     *
     * @package     base-app
     * @version     1.2
     */
    public function initialize()
    {
        // Redirect to home page if user is not admin
        if (!Auth::instance()->logged_in('admin'))
            $this->response->redirect('');

        // Check the session lifetime
        if ($this->session->has('last_active') && time() - $this->session->get('last_active') > $this->config->session->lifetime)
            $this->session->destroy();

        $this->session->set('last_active', time());

        // Set the language from session
        if ($this->session->has('lang'))
            I18n::instance()->lang($this->session->get('lang'));
        // Set the language from cookie
        elseif ($this->cookies->has('lang'))
            I18n::instance()->lang($this->cookies->get('lang')->getValue());
        
        $this->view->setVar('i18n', I18n::instance());
        $this->view->setVar('auth', Auth::instance());
    }

    /**
     * Before Action
     *
     * @package     base-app
     * @version     1.2
     */
    public function beforeExecuteRoute($dispatcher)
    {
        // Set default title
        $this->tag->setTitle('Index');
        
        // Add css and js to assets collection
        $this->assets->addCss('css/app.css');
        $this->assets->addJs('js/plugins/flashclose.js');
    }

    /**
     * Index Action 
     *
     * @package     base-app
     * @version     1.2
     */
    public function indexAction()
    {
        $this->tag->setTitle(__('Admin panel'));
    }

    /**
     * After Action
     *
     * @package     base-app
     * @version     1.2
     */
    public function afterExecuteRoute($dispatcher)
    {
        $this->tag->appendTitle(' | admin');
        
        // Minify css and js collection
        \Baseapp\Library\Tool::assetsMinification();
    }

    /**
     * Not found Action 
     *
     * @package     base-app
     * @version     1.2
     */
    public function notfoundAction()
    {
        // Send a HTTP 404 response header
        $this->response->setStatusCode(404, "Not Found");
        $this->view->setMainView('404');
    }

}