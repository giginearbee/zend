<?php

 namespace Test\Controller;

 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Test\Model\Test;          // <-- Add this import
 use Test\Form\TestForm;       // <-- Add this import
 
 class TestController extends AbstractActionController
 {
     
     protected $testTable;

     public function indexAction()
     {
         return new ViewModel(array(
             'tests' => $this->getAlbumTable()->fetchAll(),
         ));         
     }

     public function addAction()
     {
         $form = new TestForm();
         $form->get('submit')->setValue('Add');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $test = new Test();
             $form->setInputFilter($test->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $test->exchangeArray($form->getData());
                 $this->getAlbumTable()->saveAlbum($test);

                 // Redirect to list of $test
                 return $this->redirect()->toRoute('test');
             }
         }
         return array('form' => $form);

     }

     public function editAction()
     {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('test', array(
                 'action' => 'add'
             ));
         }

         // Get the Album with the specified id.  An exception is thrown
         // if it cannot be found, in which case go to the index page.
         try {
             $test = $this->getAlbumTable()->getAlbum($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('test', array(
                 'action' => 'index'
             ));
         }

         $form  = new TestForm();
         $form->bind($test);
         $form->get('submit')->setAttribute('value', 'Edit');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($test->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $this->getAlbumTable()->saveAlbum($test);

                 return $this->redirect()->toRoute('test');
             }
         }

         return array(
             'id' => $id,
             'form' => $form,
         );

     }

     public function deleteAction()
     {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('test');
         }

         $request = $this->getRequest();
         if ($request->isPost()) {
             $del = $request->getPost('del', 'No');

             if ($del == 'Yes') {
                 $id = (int) $request->getPost('id');
                 $this->getAlbumTable()->deleteAlbum($id);
             }

             return $this->redirect()->toRoute('test');
         }

         return array(
             'id'    => $id,
             'test' => $this->getAlbumTable()->getAlbum($id)
         );

     }
     
    public function getAlbumTable()
    {
        if (!$this->testTable) {
            $sm = $this->getServiceLocator();
            $this->testTable = $sm->get('Test\Model\TestTable');
        }
        return $this->testTable;
    }
     
 }