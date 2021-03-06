<?php
/**
 * Desyncr\Annotations main module
 *
 * PHP version 5.4
 *
 * @category General
 * @package  Desyncr\Annotations
 * @author   Dario Cavuotti <dc@syncr.com.ar>
 * @license  https://www.gnu.org/licenses/gpl.html GPL-3.0+
 * @version  GIT:<>
 * @link     https://me.syncr.com.ar
 */
namespace Desyncr\Annotations;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Desyncr\Annotations\Handlers\Events;
use Desyncr\Annotations\Parser\Annotations;

/**
 * Class Module
 *
 * @category General
 * @package  Desyncr\Annotations
 * @author   Dario Cavuotti <dc@syncr.com.ar>
 * @license  https://www.gnu.org/licenses/gpl.html GPL-3.0+
 * @link     https://me.syncr.com.ar
 */
class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    /**
     * On bootstrap event.
     *
     * @param MvcEvent $e MvcEvent instance
     *
     * @return mixed
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $em  = $app->getEventManager();
        $sm  = $em->getSharedManager();

        $handler = new Events($app, new Annotations($app));
        $sm->attach(
            'Zend\Mvc\Controller\AbstractActionController',
            MvcEvent::EVENT_DISPATCH,
            array(new \Desyncr\Annotations\Events\Init($handler), 'onEvent'),
            100
        );
        $em->attach(
            MvcEvent::EVENT_DISPATCH,
            array(new \Desyncr\Annotations\Events\Dispatch($handler), 'onEvent')
        );
        $em->attach(
            MvcEvent::EVENT_RENDER,
            array(new \Desyncr\Annotations\Events\Render($handler), 'onEvent')
        );
        $em->attach(
            MvcEvent::EVENT_ROUTE,
            array(new \Desyncr\Annotations\Events\Route($handler), 'onEvent')
        );
    }

    /**
     * Returns autoloader configuration.
     *
     * @return mixed
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespace' => array(__NAMESPACE__ => __DIR__)
            )
        );
    }

    /**
     * Returns Module configuration.
     *
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    /**
     * Returns service configuration.
     *
     * @return mixed
     */
    public function getServiceConfig()
    {
        return array();
    }

}
