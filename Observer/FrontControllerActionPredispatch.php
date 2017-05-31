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

namespace MSP\NoSpam\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MSP\NoSpam\Api\NoSpamInterface;
use MSP\SecuritySuiteCommon\Api\LogManagementInterface;
use Magento\Framework\Event\ManagerInterface as EventInterface;

class FrontControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var NoSpamInterface
     */
    private $noSpam;

    /**
     * @var LogManagementInterface
     */
    private $logManagement;

    /**
     * @var EventInterface
     */
    private $event;

    public function __construct(
        NoSpamInterface $noSpam,
        LogManagementInterface $logManagement,
        EventInterface $event
    ) {
        $this->noSpam = $noSpam;
        $this->logManagement = $logManagement;
        $this->event = $event;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($action = $this->noSpam->shouldCheckAction()) {
            if ($reason = $this->noSpam->shouldStopIp()) {
                // We are performing an in-place forward instead of a redirect to
                // avoid 30x codes for search engines.

                if ($action == NoSpamInterface::ACTION_STOP) {
                    $request = $observer->getRequest();
                    $request->setMethod('GET');
                    $request->initForward();
                    $request->setModuleName('msp_security_suite');
                    $request->setControllerName('stop');
                    $request->setActionName('index');
                    $request->setParams(['reason' => '' . __('Your IP has been identified as: %1', $reason)]);
                    $request->setDispatched(false);
                }

                $this->event->dispatch(LogManagementInterface::EVENT_ACTIVITY, [
                    'module' => 'MSP_NoSpam',
                    'message' => $reason,
                    'action' => $action,
                ]);
            }
        }
    }
}
