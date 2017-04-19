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

use oat\ltiClientdiag\model\ImpersonatingService;
use oat\tao\model\SessionSubstitutionService;
use \oat\taoClientDiagnostic\controller\Diagnostic as DiagnosticController;
use common_session_SessionManager as SessionManager;
use oat\oatbox\service\ServiceNotFoundException;
use oat\taoClientDiagnostic\model\storage\Storage;
use oat\taoTestTaker\models\TestTakerService;
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

        $deliveryUrl = $this->getServiceManager()->has(ImpersonatingService::SERVICE_ID)?_url('deliveries'):'';

        $data = array(
            'title'  => __('Readiness diagnostics'),
            'set'    => json_encode($diagnostics),
            'config' => json_encode($this->loadConfig()),
            'action' => 'index',
            'controller' => 'Diagnostic',
            'extension' => 'ltiClientdiag',
            'deliveryurl' => $deliveryUrl
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

    public function deliveries()
    {

        $data = array(
            'action' => 'deliveries',
            'controller' => 'Diagnostic',
            'extension' => 'ltiClientdiag',
            'deliveries' => json_encode($this->getDeliveryData())
        );

        $this->defaultData();
        $this->setData('userLabel', SessionManager::getSession()->getUserLabel());
        $this->setData('cls', 'delivery-list');
        $this->setData('data', $data);
        $this->setData('content-template', 'pages/index.tpl');
        $this->setData('content-template-ext', 'taoClientDiagnostic');
        $this->setView('layout.tpl');
    }

    private function getDeliveryData()
    {
        $deliveryData = [];

        if($this->getServiceManager()->has(ImpersonatingService::SERVICE_ID)){
            /** @var ImpersonatingService $service */
            $service = $this->getServiceManager()->get(ImpersonatingService::SERVICE_ID);
            $deliveries = $service->getDeliveries();
            foreach ($deliveries as $delivery){
                $deliveryData[] = array(
                    'text' => __('Try this delivery'),
                    'label' => $delivery->getLabel(),
                    'url' => _url('passTest', null,null, array('uri' => $delivery->getUri()))
                );
            }

        }

        return $deliveryData;

    }

    /**
     * Allow a lti user to pass a test as a test taker
     */
    public function passTest()
    {

        if(!$this->hasRequestParameter('uri')){
            throw new \common_exception_MissingParameter('uri');
        }

        try{
            /** @var ImpersonatingService $service */
            $service = $this->getServiceManager()->get(ImpersonatingService::SERVICE_ID);
            $redirectUrl = $service->getLaunchUrl($this->getRequestParameter('uri'));

            /** @var SessionSubstitutionService $sessionSubstitutionService */
            $sessionSubstitutionService = $this->getServiceManager()->get(SessionSubstitutionService::SERVICE_ID);

            $testTakerService = TestTakerService::singleton();
            $student = TestTakerService::singleton()->createInstance($testTakerService->getRootClass(), SessionManager::getSession()->getUserLabel());
            $testTakerService->setTestTakerRole($student);
            $user = new \core_kernel_users_GenerisUser($student);
            $sessionSubstitutionService->substituteSession($user);
        } catch(ServiceNotFoundException $e){
            $redirectUrl = _url('deliveries');
        }


        $this->redirect($redirectUrl);

    }


    /**
     * Return to the lti view of the deliveries
     * @throws \tao_models_classes_AccessDeniedException
     */
    public function returnToLti()
    {
        /** @var SessionSubstitutionService $sessionSubstitutionService */
        $sessionSubstitutionService = $this->getServiceManager()->get(SessionSubstitutionService::SERVICE_ID);
        if ($sessionSubstitutionService->isSubstituted()) {
            $user = SessionManager::getSession()->getUser();
            $sessionSubstitutionService->revert();
            $testTakerService = TestTakerService::singleton();
            $testTakerService->deleteResource(new \core_kernel_classes_Resource($user->getIdentifier()));
        } else {
            throw new \tao_models_classes_AccessDeniedException(SessionManager::getSession()->getUserUri(), __METHOD__, __CLASS__, 'taoClientDiagnostic');
        }

        $this->redirect(_url('deliveries'));
    }

}