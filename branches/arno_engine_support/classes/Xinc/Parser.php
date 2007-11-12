<?php
/**
 * This is the main parser that constructs a Project instance 
 * from the config file.
 *
 * @package Xinc
 * @author David Ellis
 * @author Gavin Foster
 * @author Arno Schneider
 * @version 1.0
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
require_once 'Xinc/Project.php';
require_once 'Xinc/Plugin/Repository.php';
require_once 'Xinc/Build/Status/Default.php';
require_once 'Xinc/Build/Labeler/Default.php';

class Xinc_Parser
{
    /**
     * Contains the loaded plugins.
     * Plugins are system-wide and can share information between tasks
     *
     * @var Xinc_Plugin_Interface[]
     */
    private $_plugins=array();

    /**
     * Public parse function
     *
     * @throws Xinc_Exception_MalformedConfig
     */
    public function parse($configFile)
    {
        try {
            return $this->_parse($configFile);
        }
        catch(Exception $e) {
            throw new Xinc_Exception_MalformedConfig($e->getMessage());
        }
    }

    private function _parseConfiguration(SimpleXMLElement $config)
    {
        $configurationObject = Xinc_Plugin_Repository::getInstance()->getTask('configuration', 'xinc');
        
        $this->handleConfigElements($config, $configurationObject);
       
        $configurationObject->process(new Xinc_Project());
    }

    private function _parse($configFile)
    {
        $xml = new SimpleXMLElement(file_get_contents($configFile));
        $buildStatus = 'Xinc_Project_Build_Status_Default';
        
        if (isset($xml->configuration[0])) {
            $this->_parseConfiguration($xml->configuration[0]);
        }
        
        $buildLabelerClass = 'Xinc_Project_Build_Labeler_Default';
        $projects = array();
        $plugins=array();
        foreach ($xml->project as $projXml) {
            
            $buildStatus=new $buildStatus;
            $project = new Xinc_Project();
            /**
             * Set default labeler,
             * a plugin can override it
             */
            $buildLabeler=new $buildLabelerClass;
            $buildLabeler->setBuildStatus($buildStatus);
            $project->setName((string)$projXml['name']);
            $project->setBuildLabeler($buildLabeler);
            $project->setBuildStatus($buildStatus);
            
            try {
                $this->handleElements($projXml, $project);
                $projects[] =  $project;
            }
            catch (Exception $e) {
                Xinc_Logger::getInstance()->error($e->getMessage());
            }
        }
        return $projects;
    }
    

    /**
     * Parses the task of a project-xml
     *
     * @param SimpleXmlElement $element
     * @param Xinc_Processable $project
     */
    private function handleElements(&$element,&$project)
    {

        
        
        foreach ($element->children() as $taskName => $task) {

            try{
                $taskObject = Xinc_Plugin_Repository::getInstance()->getTask($taskName, (string)$element);
            }
            catch(Exception $e){
                //var_dump($e);
                Xinc_Logger::getInstance()->error('undefined task "'
                                                 .$taskName.'"');
                throw new Xinc_Exception_MalformedConfig();
            }
            foreach ($task->attributes() as $a=>$b) {
                $setter = 'set'.$a;
                $taskObject->$setter($b);
            }

                
            $this->handleElements($task, $taskObject);
          
            $project->registerTask($taskObject);


            if ( !$taskObject->validate() ) {

                throw new Xinc_Exception_MalformedConfig('Error validating '
                                                        .'config.xml for task: '
                                                        .$taskObject->getName());

            }


        }
    }
    
    private function handleConfigElements(&$element, Xinc_Plugin_Task_Interface &$configTask)
    {

        
        
        foreach ($element->children() as $taskName => $task) {
//echo $element->getName(). ' - ' . $taskName;
            try{
                
                $taskObject = Xinc_Plugin_Repository::getInstance()->getTask($taskName, (string)$element->getName());
            }
            catch(Exception $e){
                //var_dump($e);
                Xinc_Logger::getInstance()->error('undefined task "'
                                                 .$taskName.'"');
                throw new Xinc_Exception_MalformedConfig();
            }
            foreach ($task->attributes() as $a=>$b) {
                $setter = 'set'.$a;
                $taskObject->$setter($b);
            }

                
            $this->handleConfigElements($task,$taskObject);
          
            $configTask->registerTask($taskObject);


            if ( !$taskObject->validate() ) {

                throw new Xinc_Exception_MalformedConfig('Error validating '
                                                        .'config.xml for task: '
                                                        .$taskObject->getName());

            }
            

        }
    }
    
}