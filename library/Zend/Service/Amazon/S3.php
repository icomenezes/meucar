<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon_S3
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: S3.php 24083 2011-05-30 10:52:55Z ezimuel $
 */

/**
 * @see Zend_Service_Amazon_Abstract
 */
require_once 'Zend/Service/Amazon/Abstract.php';

/**
 * @see Zend_Crypt_Hmac
 */
require_once 'Zend/Crypt/Hmac.php';

/**
 * Amazon S3 PHP connection class
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon_S3
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://docs.amazonwebservices.com/AmazonS3/2006-03-01/
 */
class Zend_Service_Amazon_S3 extends Zend_Service_Amazon_Abstract
{
    /**
     * Store for stream wrapper clients
   