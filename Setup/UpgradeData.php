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

namespace MSP\NoSpam\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use MSP\SecuritySuiteCommon\Model\ConfigMigration;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ConfigMigration
     */
    private $configMigration;

    public function __construct(
        ConfigMigration $configMigration
    ) {
        $this->configMigration = $configMigration;
    }

    protected function upgradeTo010200(ModuleDataSetupInterface $setup)
    {
        $this->configMigration->doConfigMigration(
            $setup,
            'msp_securitysuite/nospam/actions_stop_list',
            'msp_securitysuite_nospam/general/actions_stop_list'
        );
        $this->configMigration->doConfigMigration(
            $setup,
            'msp_securitysuite/nospam/actions_log_list',
            'msp_securitysuite_nospam/general/actions_log_list'
        );
        $this->configMigration->doConfigMigration(
            $setup,
            'msp_securitysuite/nospam/honeypot_enabled',
            'msp_securitysuite_nospam/honeypot/enabled'
        );
        $this->configMigration->doConfigMigration(
            $setup,
            'msp_securitysuite/nospam/honeypot_key',
            'msp_securitysuite_nospam/honeypot/key'
        );
        $this->configMigration->doConfigMigration(
            $setup,
            'msp_securitysuite/nospam/honeypot_stop_list',
            'msp_securitysuite_nospam/honeypot/stop_list'
        );
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $this->upgradeTo010200($setup);
        }

        $setup->endSetup();
    }
}
