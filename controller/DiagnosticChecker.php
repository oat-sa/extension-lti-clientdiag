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
 */

namespace oat\ltiClientdiag\controller;

use oat\taoClientDiagnostic\model\storage\Storage;
use oat\taoClientDiagnostic\exception\StorageException;
use \taoLti_models_classes_LtiLaunchData as LtiLaunchData;

/**
 * Class DiagnosticChecker
 *
 * @package oat\ltiClientdiag\controller
 */
class DiagnosticChecker extends \oat\taoClientDiagnostic\controller\DiagnosticChecker
{
    /**
     * Register data from the front end
     */
    public function storeData()
    {
        $data = $this->getData();
        $session = \common_session_SessionManager::getSession();
        if ($session instanceof \taoLti_models_classes_TaoLtiSession) {
            $contextId = $session->getLaunchData()->getVariable(LtiLaunchData::CONTEXT_ID);
            $data[Storage::DIAGNOSTIC_CONTEXT_ID] = $contextId;
        }

        $id = $this->getId();

        try {
            $storageService = $this->getServiceManager()->get(Storage::SERVICE_ID);
            $storageService->store($id, $data);
            $this->returnJson(array('success' => true, 'type' => 'success'));
        } catch (StorageException $e) {
            \common_Logger::i($e->getMessage());
            $this->returnJson(array('success' => false, 'type' => 'error'));
        }
    }
}