<?php
/**
 * Copyright (c) 2017 Open Assessment Technologies, S.A.
 *
 */

namespace oat\taoAct\scripts\install;


use oat\ltiClientdiag\model\DiagnosticService;
use oat\oatbox\extension\InstallAction;

class SetUpDiagConfig extends InstallAction
{
    public function __invoke($params)
    {
        $service = new DiagnosticService();

        $this->getServiceManager()->register( DiagnosticService::SERVICE_ID, $service);
    }
}
