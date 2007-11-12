<?php
/**
 * Main configuration class, handles the system.xml
 * 
 * @package Xinc.Project
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

require_once 'Xinc/Project/Config/File.php';
require_once 'Xinc/Project/Config/Parser.php';

class Xinc_Project_Config
{
    /**
     * Reads the system.xml
     * - parses it
     * - loads plugins
     * - loads engines
     *
     * @param string $fileName path to system.xml
     * @throws Xinc_Project_Config_Exception_FileNotFound
     * @throws Xinc_Project_Config_Exception_InvalidEntry
     */
    public static function getProjects($fileName)
    {
        $configFile = new Xinc_Project_Config_File($fileName);
        $configParser = new Xinc_Config_Parser($configFile);
        
        return $configParser->getProjects();
    }
    
   
}