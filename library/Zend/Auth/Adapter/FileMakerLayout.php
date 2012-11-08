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
 * @package    Zend_Auth
 * @subpackage Adapter
 * @copyright  Copyright (c) 2011 ClickWorks bvba
 * @license    http://www.clickworks.be/?q=new-bsd-license     New BSD License
 */


/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * @see Zend_Auth_Result
 */
require_once 'Zend/Auth/Result.php';

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage Adapter
 * @copyright  Copyright (c) 2011 ClickWorks bvba
 * @license    http://www.clickworks.be/?q=new-bsd-license     New BSD License
 */
class Zend_Auth_Adapter_FileMakerLayout implements Zend_Auth_Adapter_Interface
{

    /**
     * Database Connection
     *
     * @var FileMaker FileMaker object
     */
    protected $_fm = null;
    
    /**
     * $_dbName - the database name
     *
     * @var string
     */
    protected $_dbName = null;

    /**
     * $_tableName - the table name to check
     *
     * @var string
     */
    protected $_layoutName = null;

    /**
     * $_fieldUsername - the column to use as the username/identity
     *
     * @var string
     */
    protected $_fieldUsername = null;

    /**
     * $_fieldPassword - columns to be used as the password/credentials
     *
     * @var string
     */
    protected $_fieldPassword = null;
    
    /**
     * $_fieldId - field containing primary key of user table
     *
     * @var string
     */
    protected $_fieldId = null;
    

    /**
     * $_identity - Username value (as entered by user)
     *
     * @var string
     */
    protected $_username = null;

    /**
     * $_password - Password value (as entered by user)
     *
     * @var string
     */
    protected $_password = null;
    
    /**
     * $_preparedPassword - Password value after being processed by credentialTreatment function. Same as password if none specified)
     *
     * @var string
     */
    protected $_preparedPassword = null;
    
    /**
     * $_credentialTreatment - Treatment applied to the credential, use any of 'md5'|'sha'
     *
     * @var string
     */
    protected $_credentialTreatment = null;

    /**
     * $_authenticateResultInfo
     *
     * @var array
     */
    protected $_authenticateResultInfo = null;

    /**
     * $_fmRecord - Results of Find in database
     *
     * @var FileMaker_Result
     */
    protected $_fmResult = null;

    /**
     * $_ambiguityIdentity - Flag to indicate same Identity can be used with
     * different credentials. Default is FALSE and need to be set to true to
     * allow ambiguity usage.
     *
     * @var boolean
     */
    protected $_ambiguityUsername = false;
    
    /**
     * $_fmCmdFindSelect - Find container object, holding find criteria for username/password
     * @var FileMaker_Command_Find
     */
    protected $_fmCmdFind = null ;

    /**
     * __construct() - Sets configuration options
     *
     * @param  Zend_Db_Adapter_Abstract $zendDb If null, default database adapter assumed
     * @param  string                   $tableName
     * @param  string                   $identityColumn
     * @param  string                   $credentialColumn
     * @param  string                   $credentialTreatment
     * @return void
     */
    public function __construct(FileMaker $fm = null, $layoutName = null, $fieldUsername = null,
                                $fieldPassword = null, $fieldId= null, $passwordTreatment = null)
    {
        $this->_setDbAdapter($fm);
       
        if (null !== $layoutName) {
            $this->setLayoutName($layoutName);
        }

        if (null !== $fieldUsername) {
            $this->setFieldUsername($fieldUsername);
        }

        if (null !== $fieldPassword) {
            $this->setFieldPassword($fieldPassword);
        }
        
        if (null !== $fieldId) {
            $this->setFieldId($fieldId);
        }

        if (null !== $passwordTreatment) {
            $this->setCredentialTreatment($credentialTreatment);
        }
    }

    /**
     * _setDbAdapter() - set the database adapter to be used for quering
     *
     * @param Zend_Db_Adapter_Abstract
     * @throws Zend_Auth_Adapter_Exception
     * @return Zend_Auth_Adapter_DbTable
     */
    protected function _setDbAdapter(FileMaker $fm = null)
    {
        $this->_fm = $fm;
        return $this;
    }

    /**
     * setTableName() - set the table name to be used in the select query
     *
     * @param  string $layoutName
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setLayoutName($layoutName)
    {
        $this->_layoutName = $layoutName;
        return $this;
    }

    /**
     * setFieldUsername() - set the column name to be used as the identity column
     *
     * @param  string $userName
     * @return Zend_Auth_Adapter_FileMakerTable Provides a fluent interface
     */
    public function setFieldUsername($userName)
    {
        $this->_fieldUsername = $userName;
        return $this;
    }

    /**
     * setFieldPassword() - set the column name to be used as the credential column
     *
     * @param  string $password
     * @return Zend_Auth_Adapter_FileMakerTable Provides a fluent interface
     */
    public function setFieldPassword($password)
    {
        $this->_fieldPassword = $password;
        return $this;
    }
    
/**
     * setFieldId() - set the column name that stores primary key of user, to uniquely identify a user record
     *
     * @param  string $password
     * @return Zend_Auth_Adapter_FileMakerTable Provides a fluent interface
     */
    public function setFieldId($fieldId)
    {
        $this->_fieldId = $fieldId;
        return $this;
    }

    /**
     * setCredentialTreatment() - allows the developer to pass a transformation/encryption function that is
     * used to transform or treat the input credential data.
     *
     * In many cases, passwords and other sensitive data are encrypted, hashed, encoded,
     * obscured, or otherwise treated through some function or algorithm. 
     *
     * Use one of following strings: md5, sha
     *
     *
     * @param  string $treatment
     * @return Zend_Auth_Adapter_FileMakerTable Provides a fluent interface
     */
    public function setCredentialTreatment($treatment)
    {
        $this->_credentialTreatment = $treatment;
        return $this;
    }

    /**
     * setUsername() - set the value to be used as the username
     *
     * @param  string $value
     * @return Zend_Auth_Adapter_FileMakerTable Provides a fluent interface
     */
    public function setIdentity($value)
    {
        $this->_username = $value;
        return $this;
    }

    /**
     * setPassword() - set the credential value to be used, optionally can specify a treatment
     * to be used, should be supplied in parameterized form, such as 'MD5(?)' or 'PASSWORD(?)'
     *
     * @param  string $credential
     * @return Zend_Auth_Adapter_FileMakerTable Provides a fluent interface
     */
    public function setCredential($password)
    {
        $this->_password = $password;
        return $this;
    }

    /**
     * setAmbiguityUsername() - sets a flag for usage of identical usernames
     * with unique passwords. It accepts integers (0, 1) or boolean (true,
     * false) parameters. Default is false.
     *
     * @param  int|bool $flag
     * @return Zend_Auth_Adapter_FileMakerTable
     */
    public function setAmbiguityIdentity($flag)
    {
        if (is_integer($flag)) {
            $this->_ambiguityIdentity = (1 === $flag ? true : false);
        } elseif (is_bool($flag)) {
            $this->_ambiguityIdentity = $flag;
        }
        return $this;
    }
    /**
     * getAmbiguityUsername() - returns TRUE for usage of multiple identical
     * identies with different credentials, FALSE if not used.
     *
     * @return bool
     */
    public function getAmbiguityUsername()
    {
        return $this->_ambiguityUsername;
    }

    /**
     * getFind() - Returns the FileMaker_Command_Find object, to prepare find operation
     *
     * @return FileMaker_Command_Find
     */
    public function getFind()
    
    {
        if ($this->_fmCmdFind == null) {
            $this->_fmCmdFind = $this->_fm->newFindCommand($this->_layoutName);
        }

        return $this->_fmCmdFind;
    }

    /**
     * getResultRowObject() - Returns the result row as a stdClass object
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return stdClass|boolean
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null)
    {
        if (!$this->_resultRow) {
            return false;
        }

        $returnObject = new stdClass();

        if (null !== $returnColumns) {

            $availableColumns = array_keys($this->_resultRow);
            foreach ( (array) $returnColumns as $returnColumn) {
                if (in_array($returnColumn, $availableColumns)) {
                    $returnObject->{$returnColumn} = $this->_resultRow[$returnColumn];
                }
            }
            return $returnObject;

        } elseif (null !== $omitColumns) {

            $omitColumns = (array) $omitColumns;
            foreach ($this->_resultRow as $resultColumn => $resultValue) {
                if (!in_array($resultColumn, $omitColumns)) {
                    $returnObject->{$resultColumn} = $resultValue;
                }
            }
            return $returnObject;

        } else {

            foreach ($this->_resultRow as $resultColumn => $resultValue) {
                $returnObject->{$resultColumn} = $resultValue;
            }
            return $returnObject;

        }
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided Username.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_ResultId
     */
    public function authenticate()
    {
        $this->_authenticateSetup(); //*
        $cmdFind = $this->_authenticateCreateFind();
        $result = $cmdFind->execute();
        
        
        if (FileMaker::isError($result)) {
            require_once 'Zend/Auth/Adapter/Exception.php';
            
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo['id'] = null ;
            $this->_authenticateResultInfo['messages'][] = 'A record with the supplied username could not be found.' ;
            return $this->_authenticateCreateAuthResult();
        }
        
        if ( ($authResult = $this->_authenticateValidateResultSet($result)) instanceof Zend_Auth_Result) {
            return $authResult;
        }
        
        /* At this point, the FileMaker_Result object contains at least 1 record! */
        $resultRecords = $result->getRecords() ;

        if (true === $this->getAmbiguityUsername()) {
            $validIdentities = array ();
            foreach ($result->getRecords() as $recordUser) {
                if ($recordUser->getField($this->_fieldPassword) == $this->_preparedPassword) {
                    $validIdentities[] = $recordUser;
                }
            }
            if (!isset($validIdentities)) {
                $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                $this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
                return $this->_authenticateCreateAuthResult();
            }
            $resultRecords = $validIdentities;
        }
        
        $authResult = $this->_authenticateValidateResult(array_shift($resultRecords));
        return $authResult;
    }

    /**
     * _authenticateSetup() - This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws Zend_Auth_Adapter_Exception - in the event that setup was not done properly
     * @return true
     */
    protected function _authenticateSetup()
    {
        $exception = null;

        if ($this->_layoutName == '') {
            $exception = 'A layout must be supplied for the Zend_Auth_Adapter_FileMakerLayout authentication adapter.';
        } elseif ($this->_fieldUsername == '') {
            $exception = 'A username field must be supplied for the Zend_Auth_Adapter_FileMakerLayout authentication adapter.';
        } elseif ($this->_fieldPassword == '') {
            $exception = 'A password field must be supplied for the Zend_Auth_Adapter_FileMakerLayout authentication adapter.';
        } elseif ($this->_username == '') {
            $exception = 'A value for the username was not provided prior to authentication with Zend_Auth_Adapter_FileMakerLayout.';
        } elseif ($this->_password === null) {
            $exception = 'A password value was not provided prior to authentication with Zend_Auth_Adapter_FileMakerLayout.';
        }

        if (null !== $exception) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
            throw new Zend_Auth_Adapter_Exception($exception);
        }

        $this->_authenticateResultInfo = array(
            'code'     => Zend_Auth_Result::FAILURE,
            'Username' => $this->_username,
            'messages' => array()
            );

        return true;
    }

    /**
     * _authenticateCreateFind() - This method creates a Zend_Db_Select object that
     * is completely configured to be queried against the database.
     *
     * @return FileMaker_Command_Find
     */
    protected function _authenticateCreateFind()
    {
        // build credential expression
        if (!empty($this->_credentialTreatment)) {
            $this->_preparedPassword = $this->_preparePassword() ;
        } else {
            $this->_preparedPassword = $this->_password; 
        }

        $cmdFind = $this->getFind();
        $cmdFind->addFindCriterion($this->_fieldUsername, "==" . $this->_username) ;
        //$cmdFind->addFindCriterion($this->_fieldPassword, $password) ;
        
        return $cmdFind;
    }
    
    /**
     * _preparePassword() - Applies specified treatment (md5, sha, ...) on password
     * 
     * @return string
     */
    
    protected function _preparePassword() {
        $prepared = "" ;
        if(!empty($this->_credentialTreatment)) {
            $fn = $this->_credentialTreatment ;
            $prepared = $fn($this->_password) ;
        }
        return $prepared ;
        
    }

    /**
     * _authenticateValidateResultSet() - This method attempts to make
     * certain that only one record was returned in the resultset
     *
     * @param array $resultIdentities
     * @return true|Zend_Auth_ResultId
     */
    protected function _authenticateValidateResultSet(FileMaker_Result $result)
    {

        if ($result->getFoundSetCount() < 1) {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo['id'] = null ;
            $this->_authenticateResultInfo['messages'][] = 'A record with the supplied username could not be found.';
            return $this->_authenticateCreateAuthResult();
        } elseif ($result->getFoundSetCount() > 1 && false === $this->getAmbiguityUsername()) {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticateResultInfo['id'] = null ;
            $this->_authenticateResultInfo['messages'][] = 'More than one record matches the supplied Username.';
            return $this->_authenticateCreateAuthResult();
        }

        return true;
    }

    /**
     * _authenticateValidateResult() - This method attempts to validate that
     * the record in the resultset is indeed a record that matched the
     * Username provided to this adapter.
     *
     * @param FileMaker_Record $recordUser
     * @return Zend_Auth_ResultId
     */
    protected function _authenticateValidateResult($recordUser)
    {
        //echo $this->_preparedPassword;
        //exit() ;
        if ($recordUser->getField($this->_fieldPassword) != $this->_preparedPassword) {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $this->_authenticateResultInfo['id'] = null ;
            $this->_authenticateResultInfo['messages'][] = 'Supplied password is invalid.';
            return $this->_authenticateCreateAuthResult();
        }

        $this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
        $this->_authenticateResultInfo['id'] = $recordUser->getField($this->_fieldId) ;
        $this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
        return $this->_authenticateCreateAuthResult();
    }

    /**
     * _authenticateCreateAuthResult() - Creates a Zend_Auth_Result object from
     * the information that has been collected during the authenticate() attempt.
     *
     * @return Zend_Auth_ResultId
     */
    protected function _authenticateCreateAuthResult()
    {
        return new Zend_Auth_ResultId(
            $this->_authenticateResultInfo['code'],
            $this->_authenticateResultInfo['Username'],
            $this->_authenticateResultInfo['id'],
            $this->_authenticateResultInfo['messages']
            );
    }

}
