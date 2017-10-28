<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_NoSpam
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\NoSpam\Api;

interface NoSpamInterface
{
    const XML_PATH_ACTIONS_STOP_LIST = 'msp_securitysuite_nospam/general/actions_stop_list';
    const XML_PATH_ACTIONS_LOG_LIST = 'msp_securitysuite_nospam/general/actions_log_list';

    /**
     * Verify visitor. Return a reason if suspicious or false if ok
     * @return false|string
     */
    public function shouldStopIp();

    /**
     * Return true if should check action
     * @return false|string
     */
    public function shouldCheckAction();
}
