<?php
/**
 * CW ZEND EXTENSIONS
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.clickworks.be/?q=new-bsd-license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   CW_Zend_Extensions
 * @package    Zend_Application
 * @subpackage Adapter
 * @copyright  Copyright (c) 2011 ClickWorks bvba
 * @license    http://www.clickworks.be/?q=new-bsd-license     New BSD License
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';


/**
 * Resource for settings view options
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 *
 */
class Zend_Application_Resource_FileMaker extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var FileMaker
     */
    protected $_fm;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_fm
     */
    public function init()
    {
        $fm = $this->getFileMaker();

        return $fm;
    }

    /**
     * Retrieve FileMaker object
     *
     * @return FileMaker
     */
    public function getFileMaker()
    {
        if (null === $this->_fm) {
            $options = $this->getOptions();
            $this->_fm = new FileMaker();
            $this->_fm->setProperty('hostspec', $options['params']['host']) ;
            $this->_fm->setProperty('database', $options['params']['dbname']) ;
            $this->_fm->setProperty('username', $options['params']['username']) ;
            $this->_fm->setProperty('password', $options['params']['password']) ;
            if(isset($options['params']['loginlayoutname'] )) {
                $this->_fm->loginLayoutName =  $options['params']['loginlayoutname'] ;
            }
            if(isset($options['params']['accountfieldname'] )) {
                $this->_fm->accountFieldName =  $options['params']['accountfieldname'] ;
            }
            if(isset($options['params']['passwordfieldname'] )) {
                $this->_fm->passwordFieldName =  $options['params']['passwordfieldname'] ;
            }
            if(isset($options['params']['useridfieldname'] )) {
                $this->_fm->passwordFieldName =  $options['params']['useridfieldname'] ;
            }
                        
        }
        return $this->_fm;
    }
}
