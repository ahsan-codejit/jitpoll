<?php
namespace JitPoll;
// Add these import statements:
use JitPoll\Model\Poll;
use JitPoll\Model\PollTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module{
    public function onBootstrap($e)
    {
        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);
    }
    public function getAutoloaderConfig()    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()    {
        return include __DIR__ . '/config/module.config.php';
    }
    
        // Add this method:
    public function getServiceConfig()    {
        return array(
            'factories' => array(
                'JitPoll\Model\PollTable' =>  function($sm) {
                    $tableGateway = $sm->get('PollTableGateway');
                    $table = new PollTable($tableGateway);
                    return $table;
                },
                'PollTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Poll());
                    return new TableGateway('tbl_poll', $dbAdapter, null, $resultSetPrototype);
                },
                'PollOptionTable' =>  function($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$table     = new \JitPoll\Model\PollOptionTable($dbAdapter);
                	return $table;
                },
            ),
        );
    }

}