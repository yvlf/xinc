<?php
/**
 * The main control class.
 *
 * @package Xinc
 * @author Arno Schneider
 * @author David Ellis
 * @author Gavin Foster
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
require_once 'Xinc/Logger.php';
require_once 'Xinc/Parser.php';
require_once 'Xinc/Exception/MalformedConfig.php';
require_once 'Xinc/Plugin/Parser.php';
require_once 'Xinc/Config/Parser.php';



class Xinc2
{
    
    const DEFAULT_PROJECT_DIR = 'projects';
    const DEFAULT_STATUS_DIR = 'status';
    
    /**
     * Registry holds all the projects that the
     * Xinc instance is currently holding
     *
     * @var Xinc_Project_Registry
     */
    private static $_projectRegistry;
    /**
     * Registry holds all configured Xinc Engines
     *
     * @var Xinc_Engine_Registry
     */
    private static $_engineRegistry;
    
    /**
     * Registry holding all scheduled builds
     *
     * @var Xinc_Build_Queue_Interface
     */
    private static $_buildQueue;
    
    /**
     * Parser for system.xml
     * reads general configuration
     * options of xinc
     * + engines
     *
     * @var Xinc_Parser
     */
    private $_parser;
    
    /**
     * parses the generic <configuration/>
     * element of each xinc config file
     * and sets the overriding configuratio
     * for the specific engine
     *
     * @var Xinc_Config_Parser
     */
    private $_configParser;
    
    /**
     * Current working directory
     * containing the default xinc projects
     *
     * @var string
     */
    private $_workingDir;
    
    /**
     * Directory holding the projects
     *
     * @var string
     */
    private $_projectDir;

    
    private static $_instance;






    /**
     * The directory to drop xml status files
     * @var string
     */
    private $_statusDir;


    /**
     * Constructor.
     */
    function __construct() {
        date_default_timezone_set('UTC');
        $this->_projects = array();
        $this->_parser = new Xinc_Parser();
        $this->pluginParser = new Xinc_Plugin_Parser();
        self::$_instance = &$this;
     
        
    }
    
    
    public static function getInstance(){
        return self::$_instance;
    }
    public static function getCurrentProject(){
        return self::$_currentProject;
    }
    /**
     * Specify a config file to be parsed for project definitions.
     *
     * @param string $fileName
     * @throws Xinc_Exception_MalformedConfig
     */
    function setSystemConfigFile($fileName)
    {
        try {
            $configFile = new Xinc_Config_File($fileName);
            
            $this->_configParser = new Xinc_Config_Parser($configFile);
            
            $plugins = $this->_configParser->getPlugins();
            
            $this->_pluginParser = new Xinc_Plugin_Parser();
            
            $this->_pluginParser->parse($plugins);
            
            
        } catch(Exception $e) {
            Xinc_Logger::getInstance()->error($e->getMessage());
            throw new Xinc_Exception_MalformedConfig();
        }
    }
    function setPluginConfigFile($fileName)
    {
        try {
            $this->pluginParser->parse($fileName);
        } catch(Exception $e) {
            Xinc_Logger::getInstance()->error("error parsing plugin-tasks:"
                                             . $e->getMessage());
                
        }
    }
    /**
     * Specify multiple config files to be parsed for project definitions.
     *
     * @param string[] $fileNames
     */
    function setConfigFiles($fileNames)
    {
        foreach ($fileNames as $fileName) {
            $this->setConfigFile($fileName);
        }
    }

    /**
     * Set the directory in which to save project status files
     *
     * @param string $statusDir
     */
    function setStatusDir($statusDir)
    {
        $this->_statusDir = $statusDir;
    }

    public function getStatusDir(){
        return $this->_statusDir;
    }
    /**
     * Set the projects to build.
     *
     * @param Project[] $projects
     */
    function setProjects($projects)
    {
        $this->_projects = $projects;
    }

    /**
     * Adds the passed in project
     *
     * @param Project $project
     */
    function addProject($project)
    {
        $this->_projects[] = $project;
    }

    /**
     * Gets the projects being built
     *
     * @return Project[] $projects
     */
    function getProjects()
    {
        return $this->_projects;
    }


    /**
     * processes a single project
     * @param Project $project
     */
    function processProject(Xinc_Project &$project)
    {
        self::$_currentProject=$project;
        //if (time() < $project->getSchedule() || $project->getSchedule() == null ) return;
        //if (time() < $project->getSchedule() ) return;

        
        $buildTime = time();
        /**
         * By default a project is not processed, unless
         * a modification set sets it to PASSED
         */
        //$project->setStatus(Xinc_Project_Build_Status_Interface::STOPPED);
        $project->process(Xinc_Plugin_Slot::INIT_PROCESS);
        if ( Xinc_Project_Build_Status_Interface::STOPPED == $project->getStatus() ) {
            Xinc_Logger::getInstance()->info('Build of Project stopped'
                                             . ' in INIT phase');
            //$project->serialize();
            $project->setStatus(Xinc_Project_Build_Status_Interface::INITIAL);
            Xinc_Logger::getInstance()->setBuildLogFile(null);
            Xinc_Logger::getInstance()->flush();
            self::$_currentProject=null;
            return;
        }                                
        Xinc_Logger::getInstance()->info("CHECKING PROJECT " 
                                        . $project->getName());
        $project->process(Xinc_Plugin_Slot::PRE_PROCESS);
        
        if ( Xinc_Project_Build_Status_Interface::STOPPED == $project->getStatus() ) {
            $project->info("Build of Project stopped, "
                                             . "no build necessary");
             //$project->setBuildTime($buildTime);
            $project->setStatus(Xinc_Project_Build_Status_Interface::INITIAL);
            Xinc_Logger::getInstance()->setBuildLogFile(null);
            Xinc_Logger::getInstance()->flush();
            return;
        } else if ( Xinc_Project_Status::FAILED == $project->getStatus() ) {
            $project->error("Build failed");
            /**
             * Process failed in the pre-process phase, we need
             * to run post-process to maybe inform about the failed build
             */
            $project->process(Xinc_Plugin_Slot::POST_PROCESS);
            //$project->reschedule();
            //$project->serialize();
           
        } else if ( Xinc_Project_Status::PASSED == $project->getStatus() ) {

            $project->info("Code not up to date, "
                                            . "building project");
            $project->setBuildTime($buildTime);
            $project->process(Xinc_Plugin_Slot::PROCESS);
            if ( Xinc_Project_Status::PASSED == $project->getStatus() ) {
                $project->info("BUILD PASSED FOR PROJECT " 
                                                . $project->getName());
            } else if ( Xinc_Project_Status::STOPPED == $project->getStatus() ) {
                $project->warn("BUILD STOPPED FOR PROJECT " 
                                                . $project->getName());
            } else {
                $project->error("BUILD FAILED FOR PROJECT " 
                                                . $project->getName());
            }

            $processingPast = $project->getStatus();
            /**
             * Post-Process is run on Successful and Failed Builds
             */
            $project->process(Xinc_Plugin_Slot::POST_PROCESS);
            
            if ( $processingPast == Xinc_Project_Status::PASSED ) {
               
            
                $project->getBuildLabeler()->buildSuccessful();
                $project->getBuildStatus()->buildSuccessful();
                
            } else {
                $project->getBuildLabeler()->buildFailed();
                $project->getBuildStatus()->buildFailed();
            }
            
        }
            //$project->publish();
            //$project->reschedule();
            //$project->serialize();
            $project->setStatus(Xinc_Project_Build_Status_Interface::INITIAL);
            self::$_currentProject=null;
    }

    /**

    /**
    * Processes the projects that have been configured 
    * in the config-file and executes each project
    * if the scheduled time has expired
    *
    */
    function processProjects(){
        foreach ($this->_projects as $project ) {
            $this->processProject($project);
        }
    }
    
    public function setWorkingDir($dir)
    {
        $this->_workingDir=$dir;
    }
    
    public function setProjectDir($dir)
    {
        $this->_projectDir = $dir;
    }
    
    public function getProjectDir()
    {
        return $this->_projectDir;
    }
    
    public function getWorkingDir()
    {
        return $this->_workingDir;
    }
    /**
     * Starts the continuous loop.
     */
    protected function start($daemon)
    {
        
        if ($daemon) {
            while ( true ) {
                Xinc_Logger::getInstance()->debug('Sleeping for ' 
                                                . $minWait . ' seconds');
                //Xinc_Logger::getInstance()->flush();
                sleep((float) $minWait);
                //if ($this->_processQueue[0]->getSchedule() < time() ) {
                //    $this->processProject($this->_processQueue[0]);
               // }
                foreach ($this->_projects as $project ) {
                    if ($project->getSchedule() < time()) {
                        $this->processProject($project);
                    }
                }
                
                //usort($this->_processQueue, array(&$this,"orderProcessQueue"));
            }
        } else {
            Xinc_Logger::getInstance()->info('Run-once mode '
                                            . '(project interval is negative)');
            //Xinc_Logger::getInstance()->flush();
            $this->processProjects();
        }
    }

    /**
     * Sorts the process in the order they
     * need to be processed
     *
     * @param array $a
     * @param array $b
     * @return integer
     */
    public function orderProcessQueue($a,$b){
        if ($a->getSchedule() == $b->getSchedule()) {
            return 0;
        }
        return ($a->getSchedule() < $b->getSchedule()) ? -1 : 1;
    }
    /**
     * Static main function called by bin script
     * 
     * @param string $workingDir pointing to the base working directory
     * @param string $projectDir pointing to the directory where all the project data is
     * @param string $statusDir directory pointing to the build-statuses for the projects
     * @param string $systemConfigFile the system.xml file 
     * @param string $logFile daemon log file
     * @param integer $logLevel verbosity of the logging
     * @param boolean $daemon determins if we are running as daemon or in run-once mode
     * @param string $configFile1
     * @param string $configFile2 ...
     */
    public static function main($workingDir=null,
                                $projectDir=null,
                                $statusDir=null,
                                $systemConfigFile=null,
                                $logFile=null,
                                $logLevel=2,
                                $daemon=true)
    {
        /**
         * Set up the logging
         */
        $logger = Xinc_Logger::getInstance();
        $logger->setXincLogFile($logFile);
        $logger->setLogLevel($logLevel);
        
        if ($workingDir == null) {
            $workingDir = dirname($_SERVER['argv'][0]);
        }
        
        if ($projectDir == null) {
            $projectDir = $workingDir . DIRECTORY_SEPARATOR . self::DEFAULT_PROJECT_DIR . DIRECTORY_SEPARATOR;
        }
        if ($statusDir == null) {
            $statusDir = $workingDir . DIRECTORY_SEPARATOR . self::DEFAULT_STATUS_DIR . DIRECTORY_SEPARATOR;
        }
        
        if ($systemConfigFile == null) {
            $systemConfigFile = $workingDir . DIRECTORY_SEPARATOR . 'system.xml';
        }
        
        if ($logFile == null) {
            $logFile = $workingDir . DIRECTORY_SEPARATOR . 'xinc.log';
        }
        
        $xinc = new Xinc();
        try {
            $xinc->setWorkingDir($workingDir);
            $xinc->setProjectDir($projectDir);
            $xinc->setStatusDir($statusDir);
            $xinc->setSystemConfigFile($configFile);
            
            // get the project config files
            if (func_num_args() > 7) {
                for ($i = 7; $i < func_num_args(); $i++) {
                    $xinc->addProjectFile(func_get_arg($i));
                }
            }
            
            $xinc->start($daemon);
        } catch (Exception $e) {
            // we need to catch everything here
        }
    }
    
    
}
