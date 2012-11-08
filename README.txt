/**
 * CW ZEND EXTENSIONS
 * 
 * LICENSE
 *
 * The source files are subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.clickworks.be/?q=new-bsd-license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@clickworks.be so we can send you a copy immediately.
 *
 */
 
 OVERVIEW
 
 The directory layout of the example application follows the typical layout of a Zend Application.
 
 The class extensions included are:
 - Zend_Application_Resource_FileMaker in file library/Zend/Application/Resource/FileMaker.php
 - Zend_Auth_Adapter_FileMakerLayout in file library/Zend/Auth/Adapter/FileMakerLayout.php
 
 The example application contains a sample controller, allowing users to log in. 
 
 Please note that the Zend Framework class files are NOT bundled with this example app. 
 These have to be copy/pasted into the library/Zend directory, or - alternatively - the 
 above extension class files have to be copy/pasted into any location
 where the Zend Framework class files have been installed.
 
 
 INSTALLATION
 
 Unzip the package, and install anywhere on a webserver. Be sure to copy/paste the extension class 
 files to the Zend Library in the appropriate place. Point your browser to the installation directory 
 and append /public/Login. For example, if you installed the app folder as ZendFileMakerApp in the 
 document root of your webserver, point your browser to http://webserver/ZendFileMakerApp/public/Login

 Please note that in production environments, it is strongly recommended to create a virtual host 
 with the public folder as the document root, in order to prevent unauthorized access to the 
 application files.
 
 