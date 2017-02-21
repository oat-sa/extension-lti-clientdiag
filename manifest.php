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

use oat\tao\model\user\TaoRoles;
use oat\taoLti\models\classes\LtiRoles;
use oat\ltiClientdiag\controller\ClientdiagTool;
use oat\ltiClientdiag\controller\Diagnostic;

return [
    'name' => 'ltiClientdiag',
    'label' => 'LTI Client Diagnostic',
    'description' => 'Grants access to the client diagnostic functionality using LTI',
    'license' => 'GPL-2.0',
    'version' => '0.0.1',
    'author' => 'Open Assessment Technologies SA',
    'requires' => [
        'taoLti' => '>=1.7.1'
    ],
    'managementRole' => 'http://www.tao.lu/Ontologies/generis.rdf#ltiClientdiagManager',
    'acl' => [
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#ltiClientdiagManager', array('ext'=>'ltiClientdiag')),
        array('grant', TaoRoles::ANONYMOUS, ClientdiagTool::class),
        array('grant', LtiRoles::CONTEXT_TEACHING_ASSISTANT, Diagnostic::class),
    ],
    'install' => [
        'php' => [

        ],
        'rdf' =>[
            __DIR__ . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'ltiroles.rdf'
        ]
    ],
    'uninstall' => [],
    'update' => 'oat\\ltiClientdiag\\scripts\\update\\Updater',
    'routes' => [
        '/ltiClientdiag' => 'oat\\ltiClientdiag\\controller'
    ],
    'constants' => [
        # views directory
        "DIR_VIEWS" => __DIR__.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR,
        
        #BASE URL (usually the domain root)
        'BASE_URL' => ROOT_URL.'ltiClientdiag/',
        
        #BASE WWW required by JS
        'BASE_WWW' => ROOT_URL.'ltiClientdiag/views/'
    ]
];
