<?php

namespace TestTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Test\Model\Test;          // <-- Add this import

class TestControllerTest extends AbstractHttpControllerTestCase
{
    
    protected $traceError = true;
    
    public function setUp()
    {

        $pathfile = getcwd() . '/config/application.config.php';
        //var_dump("ij: ".$pathfile);

        $this->setApplicationConfig(
            include "$pathfile"
        );
        parent::setUp();
    }

    public function sm($methode)
    {
        $albumTableMock = $this->getMockBuilder('Test\Model\TestTable')
                                ->disableOriginalConstructor()
                                ->getMock();
        $albumTableMock->expects($this->once())
                        ->method($methode)
                        ->will($this->returnValue(array()));
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Test\Model\TestTable', $albumTableMock);
        return $serviceManager;
    }
    public function smVal($methode,$obj)
    {
        $albumTableMock = $this->getMockBuilder('Test\Model\TestTable')
                                ->disableOriginalConstructor()
                                ->getMock();
        $albumTableMock->expects($this->once())
                        ->method($methode)
                        ->will($this->returnValue($obj));
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Test\Model\TestTable', $albumTableMock);
        return $serviceManager;
    }
    
    
    public function testIndexActionCanBeAccessed()
    {
    /*    
        $albumTableMock = $this->getMockBuilder('Test\Model\TestTable')
                                ->disableOriginalConstructor()
                                ->getMock();

        $albumTableMock->expects($this->once())
                        ->method('fetchAll')
                        ->will($this->returnValue(array()));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Test\Model\TestTable', $albumTableMock);
    */
        
        $serviceManager=$this->sm('fetchAll');
        
        $this->dispatch('/test');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Test');
        $this->assertControllerName('Test\Controller\Test');
        $this->assertControllerClass('TestController');
        $this->assertMatchedRouteName('test');
    }

    public function testAddActionRedirectsAfterValidPost()
    {

        $serviceManager=$this->sm('saveAlbum');

        $postData = array(
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => '',
        );
        $this->dispatch('/test/add', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/test');
    }

    public function testEditActionRedirectsAfterValidPost()
    {

        $albumTableMock = $this->getMockBuilder('Test\Model\TestTable')
                                ->disableOriginalConstructor()
                                ->getMock();
        $test = new Test();

        $albumTableMock->expects($this->once())
                        ->method('getAlbum')
                        ->will($this->returnValue($test));
        $albumTableMock->expects($this->once())
                        ->method('saveAlbum')
                        ->will($this->returnValue(array()));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Test\Model\TestTable', $albumTableMock);

        $postData = array(
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => '2',
        );
        $this->dispatch('/test/edit/2', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/test');
    }

    public function testDeleteActionRedirectsAfterValidPost()
    {

        $albumTableMock = $this->getMockBuilder('Test\Model\TestTable')
                                ->disableOriginalConstructor()
                                ->getMock();
        $test = new Test();

        /*$albumTableMock->expects($this->once())
                        ->method('getAlbum')
                        ->will($this->returnValue($test));*/
        $albumTableMock->expects($this->once())
                        ->method('deleteAlbum')
                        ->will($this->returnValue(array()));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Test\Model\TestTable', $albumTableMock);

        $postData = array(
            'title'  => 'Led Zeppelin III',
            'artist' => 'Led Zeppelin',
            'id'     => '2',
        );
        $postData = array(
            'del'  => 'Yes',
        );
        $this->dispatch('/test/delete/2', 'POST', $postData);
        $this->assertResponseStatusCode(302);

        $this->assertRedirectTo('/test');
    }
    
}
