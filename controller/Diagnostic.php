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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA ;
 *
 */

namespace oat\ltiClientdiag\controller;

use oat\tao\model\theme\ThemeServiceInterface;
use \oat\taoClientDiagnostic\controller\Diagnostic as DiagnosticController;
use common_session_SessionManager as SessionManager;
use oat\taoClientDiagnostic\model\storage\Storage;
use oat\taoLti\models\classes\LtiRoles;
use oat\taoLti\models\classes\theme\LtiHeadless;

/**
 * Client diagnostic controller
 * @package ltiClientdiag
 */
class Diagnostic extends DiagnosticController
{
    /**
     * Display the list of all readiness checks performed on the given test center
     * It also allows launching new ones.
     *
     * @throws \common_exception_Error
     * @throws \common_exception_NoImplementation
     * @throws \common_ext_ExtensionException
     */
    public function index()
    {
        $diagnostics = $this->getDiagnosticDataTable()->getDiagnostics($this->getRequestOptions());

        $data = array(
            'title'  => __('Readiness diagnostics'),
            'set'    => json_encode($diagnostics),
            'config' => json_encode($this->loadConfig()),
            'action' => 'index',
            'controller' => 'Diagnostic',
            'extension' => 'ltiClientdiag'
        );

        $userLabel = SessionManager::getSession()->getUserLabel();

        $this->defaultData();
        $this->setData('cls', 'diagnostic-index');
        $this->setData('userLabel', $userLabel);
        $this->setData('data', $data);
        $this->setData('content-template', 'pages/index.tpl');
        $this->setData('content-template-ext', 'taoClientDiagnostic');
        $this->setData('showControls', $this->showControls());
        $this->setView('layout.tpl');
    }

    /**
     * Display the diagnostic runner
     * @throws \common_exception_Error
     * @throws \common_ext_ExtensionException
     */
    public function diagnostic()
    {
        $data = array(
            'title'  => __('Readiness Check'),
            'config' => json_encode($this->loadConfig()),
            'action' => 'diagnostic',
            'controller' => 'Diagnostic',
            'extension' => 'ltiClientdiag'
        );

        /** @var ThemeServiceInterface $themeService */
        $themeService = $this->getServiceManager()->get(ThemeServiceInterface::SERVICE_ID);
        $theme = $themeService->getTheme();
        $configurableText = $theme->getAllTexts();
        $this->setData('configurableText', json_encode($configurableText));
        $this->setData('showControls', $this->showControls());

        $this->defaultData();
        $this->setData('userLabel', SessionManager::getSession()->getUserLabel());
        $this->setData('cls', 'diagnostic-runner');
        $this->setData('data', $data);
        $this->setData('content-template', 'pages/index.tpl');
        $this->setData('content-template-ext', 'taoClientDiagnostic');
        $this->setView('layout.tpl');
    }

    /**
     * @return array
     * @throws \common_ext_ExtensionException
     */
    protected function loadConfig()
    {
        $config = array_merge_recursive(
            parent::loadConfig(),
            \common_ext_ExtensionsManager::singleton()->getExtensionById('ltiClientdiag')->getConfig('clientDiag')
        );

        return $config;
    }

    /**
     * @param array $defaults
     * @return array
     * @throws \common_exception_Error
     */
    protected function getRequestOptions(array $defaults = [])
    {
        $options = parent::getRequestOptions($defaults);
        $user = SessionManager::getSession()->getUser();

        if (!in_array(LtiRoles::CONTEXT_TEACHING_ASSISTANT, $user->getRoles())) {
            $user = \common_session_SessionManager::getSession()->getUser();
            $userId = $user->getIdentifier();
            $options['filter'] = [Storage::DIAGNOSTIC_USER_ID => $userId];
        }

        return $options;
    }

    /**
     * Defines if the top and bottom action menu should be displayed or not
     *
     * @return boolean
     */
    protected function showControls() {
        $themeService = $this->getServiceManager()->get(ThemeServiceInterface::SERVICE_ID);
        if ($themeService instanceof ThemeServiceInterface || $themeService instanceof LtiHeadless) {
            return !$themeService->isHeadless();
        }
        return false;
    }
}
