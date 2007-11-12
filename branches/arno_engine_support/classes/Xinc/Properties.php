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
class Xinc_Properties
{
    private $_properties;
    
    /**
     * Sets a property value
     *
     * @param string $property
     * @param mixed $value
     */
    public function set($property, $value)
    {
        $this->_properties[$property] = $value;
    }
    
    /**
     * Returns the value of the queried property
     *
     * @param string $property
     * @return string or null if property not found
     */
    public function get($property)
    {
        if (isset($this->_properties[$property])) {
            return $this->_properties[$property];
        } else {
            return null;
        }
    }
}