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
use oat\ltiClientdiag\model\LtiClientDiagnosticRoles;
use oat\taoLti\models\classes\LtiRoles;

/**
 * Class Updater
 * @package oat\ltiProctoring\scripts\update
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
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
            \tao_models_classes_RoleService::singleton()->unincludeRole(new \core_kernel_classes_Resource(LtiRoles::CONTEXT_TEACHING_ASSISTANT), new \core_kernel_classes_Resource(LtiClientDiagnosticRoles::LTI_CLIENTDIAG_MANAGER));
            $this->setVersion('0.1.2');
        }
    }
}
