<?php
/**
 * This class represents the build that is going to be run
 * with Xinc
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
require_once 'Xinc/Build/Interface.php';
require_once 'Xinc/Build/Properties.php';
require_once 'Xinc/Build/Exception/NotRun.php';
require_once 'Xinc/Build/Exception/Serialization.php';

class Xinc_Build implements Xinc_Build_Interface
{
    /**
     * @var Xinc_Engine_Interface
     */
    private $_engine;
    
    /**
     * @var Xinc_Project
     */
    private $_project;
    
    /**
     * @var Xinc_Build_Properties
     */
    private $_properties;
    
    /**
     * 
     *
     * @var integer
     */
    private $_buildTimestamp;
    
    /**
     * 
     *
     * @var integer
     */
    private $_nextBuildTimestamp;
    
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
                                $buildTimestamp=null)
    {
        $this->_engine = $engine;
        $this->_project = $project;
        $this->_buildTimestamp = $buildTimestamp;
        $this->_properties = new Xinc_Build_Properties();
    }
    
    /**
     * Returns the last build
     * @return Xinc_Build_Interface
     */
    public function &getLastBuild()
    {
        
    }
    /**
     *
     * @return Xinc_Build_Properties
     */
    public function &getProperties()
    {
        return $this->_properties;
    }
     /**
     * sets the build time for this build
     *
     * @param integer $buildTime unixtimestamp
     */
    public function setBuildTime($buildTime)
    {
        $this->_buildTimestamp = $buildTime;
    }
    /**
     * returns the timestamp of this build
     * @return integer Timestamp of build (unixtimestamp)
     */
    public function getBuildTime()
    {
        return $this->_buildTimestamp;
    }
    
    /**
     * sets the next build time for this build
     *
     * @param integer $buildTime unixtimestamp
     */
    public function setNextBuildTime($buildTime)
    {
        $this->_nextBuildTimestamp = $buildTime;
    }

    /**
     * Returns the next build time (unix timestamp)
     * for this build
     *
     */
    public function getNextBuildTime()
    {
        return $this->_nextBuildTimestamp;
    }
    /**
     * 
     * @return Xinc_Project
     */
    public function &getProject()
    {
        return $this->_project;
    }
    
    /**
     * 
     * @return Xinc_Engine_Interface
     */
    public function &getEngine()
    {
        return $this->_engine;
    }
    
    /**
     * stores the build information
     *
     * @throws Xinc_Build_Exception_NotRun
     * @throws Xinc_Build_Exception_Serialization
     */
    public function serialize()
    {
        if (!in_array($this->getStatus(), array(self::PASSED, self::FAILED))) {
            throw new Xinc_Build_Exception_NotRun();
        } else if ($this->getBuildTime() == null) {
            throw new Xinc_Build_Exception_Serialization();
        }
        $statusDir = 'test';//Xinc::getInstance()->getStatusDir();
        
        $yearMonthDay = date("Ymd", $this->getBuildTime());
        $subDirectory = $this->getProject()->getName();
        $subDirectory .= DIRECTORY_SEPARATOR;
        $subDirectory .= $yearMonthDay;
        
        
        $fileName = $statusDir . DIRECTORY_SEPARATOR . $subDirectory
                  . DIRECTORY_SEPARATOR . $this->getBuildTime()
                  . DIRECTORY_SEPARATOR . 'status.ser';
                  
        $contents = serialize($this);
        
        return $contents;
    }
    
    /**
     * loads the build information
     *
     */
    public function unserialize()
    {
        
    }
    
    /**
     * returns the status of this build
     *
     */
    public function getStatus()
    {
        
    }
    
    /**
     * Set the status of this build
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        
    }
    
    /**
     * Executes the build
     *
     */
    public function run()
    {
        
    }
    
    public function __sleep()
    {
        /**
         * minimizing the storage for the project,
         * we just want the name
         */
        $project = new Xinc_Project();
        $project->setName($this->getProject()->getName());
        $this->_project = $project;
        
        return array('_project', '_buildTimestamp', '_properties');
    }
}