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

namespace oat\ltiClientdiag\controller;

/**
 * Class ClientdiagTool
 * @package oat\ltiClientdiag\controller
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class ClientdiagTool extends \taoLti_actions_ToolModule
{
    /**
     * run client diagnostic
     */
    public function run()
    {
        if ($this->hasAccess(Diagnostic::class, 'diagnostic')) {
            $this->redirect(_url('diagnostic', 'Diagnostic'));
        } else {
            $this->returnError(__('You are not authorized to access this resource'));
        }
    }
}