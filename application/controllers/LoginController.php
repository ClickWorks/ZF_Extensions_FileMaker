<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // Create login form
        $form = new Zend_Form;
        $form->setMethod('post') ;
        $form->addElement('text', 'username', array('label'=>'Username: ', 'required'=>true)) ;
        $form->addElement('password', 'password', array('label'=>'Password: ', 'required'=>true)) ;
        $form->addElement('submit', 'submit', array('ignore'=>true, 'label'=>'Login')) ;
        
        $this->view->feedback = "Please enter your username and password" ;
        
        if($this->getRequest()->isPost() && $form->isValid($_POST)) {
            
            $fm = $this->getInvokeArg('bootstrap')->getResource('FileMaker') ;
            $layoutName =  $fm->loginLayoutName ; // Resource parameter
            $accountFieldName = $fm->accountFieldName;
            $passwordFieldName = $fm->passwordFieldName ;
            $useridFieldName = $fm->useridFieldName;
                
            $adapter = new Zend_Auth_Adapter_FileMakerLayout($fm, $layoutName, $accountFieldName, $passwordFieldName, $useridFieldName) ;
            // Load username/password entered by user on form
            $adapter->setIdentity($form->getValue('username')); 
            $adapter->setCredential($form->getValue('password')); 
            $adapter->setCredentialTreatment('') ;
            
            $adapter->setAmbiguityIdentity(true) ; // Multiple accounts may share accountname; combination of accountname + password must be unique
            
            $moduleNamespace = new Zend_Session_Namespace('MyApp') ;
            $result = $adapter->authenticate() ;
            
            if($result->isValid()) {
                 // Login successful!
                 $moduleNamespace->bLogin = true ;
                 $moduleNamespace->idUser = $result->getId();
                 $moduleNamespace->loginName = $form->getValue('username');
                 $this->view->feedback = "Successful Login" ;
                 //Redirect to whatever page you want
                 $this->_helper->getHelper('Redirector')->setGotoSimple("home", "Login") ;
            } else {
                // Login not successful
                $msg = implode(', ' , $result->getMessages()) ;
                $this->view->feedback = $msg ;
                $moduleNamespace->bLogin = false ;
            }
            
            
        }
        $this->view->form = $form ;
        
    }
    
    public function homeAction() {
        
    }
   
}