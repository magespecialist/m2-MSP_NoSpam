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

namespace MSP\NoSpam\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use MSP\NoSpam\Api\NoSpamInterface;
use MSP\NoSpam\Api\ProviderInterface;

class NoSpam implements NoSpamInterface
{
    const XML_PATH_ACTIONS_STOP_LIST = 'msp_securitysuite/nospam/actions_stop_list';
    const XML_PATH_ACTIONS_LOG_LIST = 'msp_securitysuite/nospam/actions_log_list';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $providers;

    /**
     * @var CacheType
     */
    private $cacheType;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RemoteAddress $remoteAddress,
        RequestInterface $request,
        CacheType $cacheType,
        $providers = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->providers = $providers;
        $this->cacheType = $cacheType;
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
    }

    /**
     * Return true if should check action
     * @return false|string
     */
    public function shouldCheckAction()
    {
        $fullActionName = strtolower($this->request->getFullActionName());

        $stopList = trim(strtolower($this->scopeConfig->getValue(static::XML_PATH_ACTIONS_STOP_LIST)));
        $logList = trim(strtolower($this->scopeConfig->getValue(static::XML_PATH_ACTIONS_LOG_LIST)));

        $stopsList = preg_split('/[\W\s\n\r\,]+/', $stopList);
        $logsList = preg_split('/[\W\s\n\r\,]+/', $logList);

        if (in_array($fullActionName, $stopsList)) {
            return NoSpamInterface::ACTION_STOP;
        }

        if (in_array($fullActionName, $logsList)) {
            return NoSpamInterface::ACTION_LOG;
        }

        return false;
    }

    /**
     * Verify visitor. Return a reason if suspicious or false if ok
     * @return false|string
     */
    public function shouldStopIp()
    {
        $remoteIp = $this->remoteAddress->getRemoteAddress();

        foreach ($this->providers as $providerName => $provider) {
            if ($provider->isEnabled()) {
                /** @var ProviderInterface $provider */
                if ($reason = $provider->shouldStopIp($remoteIp)) {
                    return $reason;
                }
            }
        }

        return false;
    }
}
