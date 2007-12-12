<?php
/**
 * Parser for Xinc Engines, defined in an xml file
 * 
 * @package Xinc.Engine
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
require_once 'Xinc/Engine/Repository.php';
require_once 'Xinc/Engine/Exception/FileNotFound.php';
require_once 'Xinc/Engine/Exception/ClassNotFound.php';
require_once 'Xinc/Engine/Exception/Invalid.php';


class Xinc_Engine_Parser
{
    
    /**
     * Public parse function
     * 
     * @param  Xinc_Config_Element_Iterator $xml
     * @throws Xinc_Plugin_Task_Exception
     * @throws Xinc_Plugin_Exception_FileNotFound
     * @throws Xinc_Plugin_Exception_Invalid
     * @throws Xinc_Plugin_Exception_ClassNotFound
     */
    public static function parse(Xinc_Config_Element_Iterator $iterator)
    {
        
        while ($iterator->hasNext()) {
            self::_loadEngine($iterator->next());
        }
  
    }

    /**
     * Checks whether a file has been included or not
     *
     * @param string $filename
     * @return boolean
     */
    private static function _findIncluded($filename)
    {
        $includedFiles = get_included_files();
        
        foreach ($includedFiles as $file) {
            if (strstr($file, $filename)) return true;
        }
        return false;
    }
    
    /**
     * Enter description here...
     *
     * @param SimpleXMLElement $pluginXml
     */
    private static function _loadEngine(SimpleXMLElement $pluginXml)
    {
        $plugins = array();
        
        $attributes = $pluginXml->attributes();
        
        
        $res = @include_once((string)$attributes->filename);
        
        $included = self::_findIncluded((string)$attributes->filename);
        
        if ($res !== true && !$included) {
            
            throw new Xinc_Engine_Exception_FileNotFound((string)$attributes->classname,
                                                         (string)$attributes->filename);
        }
        if (!class_exists((string)$attributes->classname)) {
            
            throw new Xinc_Engine_Exception_ClassNotFound((string)$attributes->classname,
                                                          (string)$attributes->filename);
        }
        
        $classname = (string) $attributes->classname;
        
        $default = isset($attributes->default) ? ((string) $attributes->default == 'default' ? true : false) : false;
        
        $engine = new $classname;
        
        if (!$engine instanceof Xinc_Engine_Interface) {
            
            throw new Xinc_Engine_Exception_Invalid((string)$attributes->classname);
        }
        
        Xinc_Engine_Repository::getInstance()->registerEngine($engine, $default);

    }

    
}