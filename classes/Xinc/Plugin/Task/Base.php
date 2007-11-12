<?php
/**
 * PUT DESCRIPTION HERE
 * 
 * @package Xinc
 * @author Arno Schneider
 * @version 2.0
 * @copyright 2007 David Ellis, One Degree Square
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
require_once 'Xinc/Plugin/Task/Interface.php';

abstract class Xinc_Plugin_Task_Base implements Xinc_Plugin_Task_Interface
{
    protected $_subtasks = array();
    protected $_plugin;
    public function getClassname()
    {
        return get_class($this);
    }
    
    public function getName()
    {
        return strtolower($this->getClassname());
    }
    /**
     * Support for subtasks, empty by default
     * needs to be overriden if needed in the extending class
     *
     * @param Xinc_Plugin_Task_Interface $task
     */
    public function registerTask(Xinc_Plugin_Task_Interface  &$task)
    {
        $this->_subtasks[] = $task;
    }
    /**
     * Constructor, stores a reference to the plugin for
     * usage of functionality
     *
     * @param Xinc_Plugin_Interface $plugin
     */
    public function __construct(Xinc_Plugin_Interface &$plugin){
        $this->_plugin = $plugin;
    }
    
    public function getAllowedParentElements()
    {
        return array();
    }
}