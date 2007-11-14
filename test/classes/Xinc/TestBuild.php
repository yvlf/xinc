<?php
/**
 * Test Class for the Xinc Build Properties
 * 
 * @package Xinc.Build
 * @author Arno Schneider
 * @version 2.0
 * @copyright 2007 Arno Schneider, Barcelona
 * @license  http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *    This file is part of Xinc.
 *    Xinc is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as published
 *    by the Free Software Foundation; either version 2.1 of the License, or    
 *    (at your option) any later version.
 *
 *    Xinc is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with Xinc, write to the Free Software
 *    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once 'Xinc/Build.php';
require_once 'Xinc/Project.php';
require_once 'Xinc/Engine/Sunrise.php';

require_once 'PHPUnit/Framework/TestCase.php';

class Xinc_TestBuild extends PHPUnit_Framework_TestCase
{
    
   
    public function testBuild()
    {
        $project = new Xinc_Project();
        $project->setName('test');
        $build = new Xinc_Build(new Xinc_Engine_Sunrise(),$project);
        $build->setBuildTime(time());
        $build->getProperties()->set('test',1);
        
        $serializedObject = $build->serialize();
        
        $object = unserialize($serializedObject);
        
        $this->assertEquals($build->getProject()->getName(), $object->getProject()->getName(),
                            'Project Name should have gotten serialized');
        $this->assertEquals($build->getProperties()->get('test'),
                            $object->getProperties()->get('test'),
                           'Properties should be equal');
    }

   
   
}