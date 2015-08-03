<?php
namespace Test;

 use Test\Model\Test;
 use Test\Model\TestTable;
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\TableGateway\TableGateway;
 
class Module
{
    public function getAutoloaderConfig()
    {
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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Test\Model\TestTable' =>  function($sm) {
                    $tableGateway = $sm->get('TestTableGateway');
                    $table = new TestTable($tableGateway);
                    return $table;
                },
                'TestTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Test());
                    return new TableGateway('test', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
 
 
}