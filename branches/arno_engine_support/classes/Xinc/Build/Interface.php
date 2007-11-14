<?php
/**
 * Build interface
 * 
 * Used by the engines to process a build
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
interface Xinc_Build_Interface
{
    const INITIALIZED=-2;
    const FAILED=0;
    const PASSED=1;
    const STOPPED=-1;

     /** 
     * sets the project, engine
     * and timestamp for the build
     *
     * @param Xinc_Engine_Interface $engine
     * @param Xinc_Project $project
     * @param integer $buildTimestamp
     */
    public function __construct(Xinc_Engine_Interface &$engine,
                                Xinc_Project &$project,
                                $buildTimestamp=null);
    
    /**
     * Returns the last build
     * @return Xinc_Build_Interface
     */
    public function &getLastBuild();
    
    /**
     * returns the build properties
     *
     * @return Xinc_Build_Properties
     */
    public function &getProperties();
    /**
     * sets the build time for this build
     *
     * @param integer $buildTime unixtimestamp
     */
    public function setBuildTime($buildTime);
    
    /**
     * returns the timestamp of this build
     * @return integer Timestamp of build (unixtimestamp)
     */
    public function getBuildTime();
    
    /**
     * sets the next build time for this build
     *
     * @param integer $buildTime unixtimestamp
     */
    public function setNextBuildTime($buildTime);

    /**
     * Returns the next build time (unix timestamp)
     * for this build
     *
     */
    public function getNextBuildTime();
    /**
     * 
     * @return Xinc_Project
     */
    public function &getProject();
    
    /**
     * 
     * @return Xinc_Engine_Interface
     */
    public function &getEngine();
    
    /**
     * stores the build information
     *
     */
    public function serialize();
    
    /**
     * loads the build information
     *
     */
    public function unserialize();
    
    /**
     * returns the status of this build
     *
     */
    public function getStatus();
    
    /**
     * Set the status of this build
     *
     * @param integer $status
     */
    public function setStatus($status);
    
    /**
     * Executes the build
     *
     */
    public function run();
}