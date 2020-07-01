<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\ltiClientdiag\scripts\update;
use oat\ltiClientdiag\model\DiagnosticService;
use oat\ltiClientdiag\model\LtiClientDiagnosticRoles;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoLti\models\classes\LtiRoles;

/**
 * Class Updater
 * @package oat\ltiProctoring\scripts\update
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 * @deprecated use migrations instead. See https://github.com/oat-sa/generis/wiki/Tao-Update-Process
 */
class Updater extends \common_ext_ExtensionUpdater
{

    /**
     * @param $initialVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion)
    {
        if ($this->isVersion('0.0.1')) {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('ltiClientdiag');
            $extension->setConfig('clientDiag', [
                'extension' => 'ltiClientdiag'
            ]);

            $this->setVersion('0.1.0');
        }

        $this->skip('0.1.0', '0.1.1');

        if ($this->isVersion('0.1.1')) {
            OntologyUpdater::syncModels();
            AclProxy::applyRule(new AccessRule('grant', LtiRoles::CONTEXT_TEACHING_ASSISTANT, ['ext'=>'ltiClientdiag']));
            $this->setVersion('0.1.2');
        }

        $this->skip('0.1.2', '1.0.0');

        if ($this->isVersion('1.0.0')) {
            AclProxy::applyRule(new AccessRule('grant', LtiRoles::CONTEXT_LEARNER, ['ext'=>'ltiClientdiag']));
            $this->setVersion('1.1.0');
        }

        $this->skip('1.1.0', '1.2.2');

        if($this->isVersion('1.2.2')){
            $service = new DiagnosticService();

            $this->getServiceManager()->register(DiagnosticService::SERVICE_ID, $service);
            $this->setVersion('1.3.0');
        }

        $this->skip('1.3.0', '2.0.1');

        if ($this->isVersion('2.0.1')) {
            // Update clientDiag.conf.php
            $extension = $this->getServiceManager()
                ->get(\common_ext_ExtensionsManager::SERVICE_ID)
                ->getExtensionById('ltiClientdiag');
            $oldClientDiagConfig = $extension->getConfig('clientDiag');

            $extension->setConfig('clientDiag', [
                'diagnostic' => $oldClientDiagConfig,
            ]);

            $this->setVersion('2.0.2');
        }

        $this->skip('2.0.2', '2.0.3');

        
        //Updater files are deprecated. Please use migrations.
        //See: https://github.com/oat-sa/generis/wiki/Tao-Update-Process

        $this->setVersion($this->getExtension()->getManifest()->getVersion());
    }
}
