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
 */

namespace oat\ltiClientdiag\model;

use oat\tao\model\theme\ThemeService;
use oat\taoClientDiagnostic\model\diagnostic\DiagnosticService as ParentDiagnosticService;
use oat\taoClientDiagnostic\model\diagnostic\DiagnosticServiceInterface;
use oat\taoLti\models\classes\theme\LtiThemeDetailsProvider;

class DiagnosticService extends ParentDiagnosticService implements DiagnosticServiceInterface
{

    /**
     * @inheritdoc
     */
    public function getTesters()
    {
        $testers = parent::getTesters();
        $session = \common_session_SessionManager::getSession();
        if ($session instanceof \taoLti_models_classes_TaoLtiSession) {
            $launchData = $session->getLaunchData();
            $theme = ($launchData->hasVariable(LtiThemeDetailsProvider::LTI_CUSTOM_THEME_VARIABLE)) ? $launchData->getVariable(LtiThemeDetailsProvider::LTI_CUSTOM_THEME_VARIABLE) : $this->getDefaultTheme();
            $samples = $testers['testers']['performance']['samples'];
            if (is_array(reset($samples))) {
                if (array_key_exists($theme, $samples)) {
                    $testers['testers']['performance']['samples'] = $samples[$theme];
                } else {
                    $testers['testers']['performance']['samples'] = array_shift($samples);
                }
            }
        }

        return $testers;

    }

    protected function getDefaultTheme()
    {
        /** @var ThemeService $themeService */
        $themeService = $this->getServiceManager()->get(ThemeService::SERVICE_ID);
        $themeId = $themeService->getOption(ThemeService::OPTION_CURRENT);
        return $themeId;
    }
}