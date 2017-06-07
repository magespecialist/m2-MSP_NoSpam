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
use MSP\NoSpam\Api\ProviderInterface;
use MSP\NoSpam\Model\Config\Source\StopList;

class Honeypot implements ProviderInterface
{
    const XML_PATH_ENABLED = 'msp_securitysuite/nospam/honeypot_enabled';
    const XML_PATH_KEY = 'msp_securitysuite/nospam/honeypot_key';
    const XML_PATH_STOP_LIST = 'msp_securitysuite/nospam/honeypot_stop_list';

    const DNS_DOMAIN = 'dnsbl.httpbl.org';

    const TYPE_SEARCH_ENGINE = 0;
    const TYPE_SUSPICIOUS = 1;
    const TYPE_HARVESTER = 2;
    const TYPE_SPAMMER = 3;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StopList
     */
    private $stopList;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StopList $stopList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->stopList = $stopList;
    }

    /**
     * Return true if AV is enabled
     * @return bool
     */
    public function isEnabled()
    {
        return !!$this->scopeConfig->getValue(static::XML_PATH_ENABLED);
    }

    /**
     * Verify visitor. Return a reason if suspicious or false if ok
     * @param $ip
     * @return false|string
     */
    public function shouldStopIp($ip)
    {
        $key = $this->scopeConfig->getValue(static::XML_PATH_KEY);

        $queryHost =
            $key . "." . implode(".", array_reverse(explode(".", $ip))) .
            "." . static::DNS_DOMAIN;

        $res = explode(".", gethostbyname($queryHost));
        if ($res[0] != '127') { // Not matched
            return false;
        }

        $visitorType = intval($res[3]);

        $stopList = explode(',', $this->scopeConfig->getValue(static::XML_PATH_STOP_LIST));
        $categories = [];

        if ($visitorType == 0) {
            $categories[] = static::TYPE_SEARCH_ENGINE;
        }

        if ($visitorType & 0b1) {
            $categories[] = static::TYPE_SUSPICIOUS;
        }

        if ($visitorType & 0b10) {
            $categories[] = static::TYPE_HARVESTER;
        }

        if ($visitorType & 0b100) {
            $categories[] = static::TYPE_SPAMMER;
        }

        $matchedCategories = array_intersect($stopList, $categories);
        if (count($matchedCategories)) {
            $options = $this->stopList->toArray();
            return $options[array_shift($matchedCategories)];
        }

        return false;
    }
}
