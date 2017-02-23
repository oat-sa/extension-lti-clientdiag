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

use \oat\taoClientDiagnostic\controller\Diagnostic as DiagnosticController;
use common_session_SessionManager as SessionManager;
use oat\oatbox\service\ServiceNotFoundException;
use oat\taoClientDiagnostic\model\storage\Storage;
use \taoLti_models_classes_LtiLaunchData as LtiLaunchData;

/**
 * Client diagnostic controller
 * @package ltiClientdiag
 */
class Diagnostic extends DiagnosticController
{
    /**
     * Display the list of all readiness checks performed on the given test center
     * It also allows launching new ones.
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
        $this->setView('layout.tpl');
    }

    /**
     * Display the diagnostic runner
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
     */
    protected function loadConfig()
    {
        $config = array_merge(
            parent::loadConfig(),
            \common_ext_ExtensionsManager::singleton()->getExtensionById('ltiClientdiag')->getConfig('clientDiag')
        );

        return $config;
    }

    /**
     * @param array $defaults
     * @return array
     */
    protected function getRequestOptions(array $defaults = [])
    {
        $options = parent::getRequestOptions($defaults);
        $session = \common_session_SessionManager::getSession();
        $contextId = $session->getLaunchData()->getVariable(LtiLaunchData::CONTEXT_ID);
        $options['filter'] = [Storage::DIAGNOSTIC_CONTEXT_ID => $contextId];
        return $options;
    }

}