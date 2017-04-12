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

use oat\oatbox\service\ConfigurableService;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;

class ImpersonatingService extends ConfigurableService
{
    const SERVICE_ID = 'ltiClientdiag/ImpersonatingService';

    const LAUNCH_URL_EXTENSION = 'extension';
    const LAUNCH_URL_CONTROLLER = 'controller';
    const LAUNCH_URL_ACTION = 'action';
    const DELIVERY_PROPERTY = 'delivery_property';
    const DELIVERY_PROPERTY_VALUE = 'delivery_property_value';

    public function getLaunchUrl($deliveryUri)
    {
        return _url(
            $this->getOption(self::LAUNCH_URL_ACTION),
            $this->getOption(self::LAUNCH_URL_CONTROLLER),
            $this->getOption(self::LAUNCH_URL_EXTENSION),
            array('uri'=>$deliveryUri));

    }

    public function getDeliveries()
    {
        $deliveries = [];
        if(!is_null($this->getOption(self::DELIVERY_PROPERTY))){
            DeliveryAssemblyService::singleton()->getRootClass()->searchInstances(
                [
                    $this->getOption(self::DELIVERY_PROPERTY) => $this->getOption(self::DELIVERY_PROPERTY_VALUE)
                ],
                true
            );
        } else {
            $deliveries = DeliveryAssemblyService::singleton()->getAllAssemblies();
        }

        return $deliveries;
    }

}