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
use MSP\SecuritySuiteCommon\Api\AlertInterface;
use MSP\SecuritySuiteCommon\Api\LockDownInterface;

class FrontControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var NoSpamInterface
     */
    private $noSpam;

    /**
     * @var LockDownInterface
     */
    private $lockDown;

    /**
     * @var AlertInterface
     */
    private $alert;

    public function __construct(
        NoSpamInterface $noSpam,
        AlertInterface $alert,
        LockDownInterface $lockDown
    ) {
        $this->noSpam = $noSpam;
        $this->lockDown = $lockDown;
        $this->alert = $alert;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($action = $this->noSpam->shouldCheckAction()) {
            if ($reason = $this->noSpam->shouldStopIp()) {
                $this->alert->event(
                    'MSP_NoSpam',
                    'IP identified as: ' . $reason,
                    AlertInterface::LEVEL_WARNING,
                    null,
                    $action
                );

                if ($action == AlertInterface::ACTION_LOCKDOWN) {
                    /** @var \Magento\Framework\App\Action\Action $controllerAction */
                    $controllerAction = $observer->getEvent()->getControllerAction();
                    $this->lockDown->doActionLockdown(
                        $controllerAction,
                        __('Your IP has been identified as: %1', $reason)
                    );
                }
            }
        }
    }
}
