<?php
/**
 * This is the main parser that constructs a Project instance from the config file.
 *
 * @package Xinc
 * @author David Ellis
 * @author Gavin Foster
 * @version 1.0
 * @copyright 2007 David Ellis, One Degree Square
 * @license  http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *	This file is part of Xinc.
 *	Xinc is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU Lesser General Public License as published by
 *	the Free Software Foundation; either version 2.1 of the License, or
 *	(at your option) any later version.
 *
 *	Xinc is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Lesser General Public License for more details.
 *
 *	You should have received a copy of the GNU Lesser General Public License
 *	along with Xinc, write to the Free Software
 *	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
require_once 'Xinc/Project.php';
require_once 'Xinc/Plugin/Repository.php';

class Xinc_Parser
{
	/**
	 * Contains the loaded plugins.
	 * Plugins are system-wide and can share information between tasks
	 *
	 * @var Xinc_Plugin_Interface[]
	 */
	private $plugins=array();

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
			throw new Xinc_Exception_MalformedConfig();
		}
	}



	private function _parse($configFile)
	{
		$project = new Xinc_Project();
		$xml = new SimpleXMLElement(file_get_contents($configFile));

		$projects = array();
		$plugins=array();
		foreach ($xml->project as $projXml) {
				
			$this->handleElements($projXml,$project);
				

				

			$project->setInterval($projXml['interval']);
			$project->setName($projXml['name']);
				
			$projects[] =  $project;
		}
		return $projects;
	}

	/**
	 * Parses the task of a project-xml
	 *
	 * @param SimpleXmlElement $element
	 * @param Xinc_Processable $project
	 */
	private function handleElements(&$element,&$project){

		foreach ($element->children() as $task) {

			try{
				$taskObject=Xinc_Plugin_Repository::getInstance()->getTask($task->getName());
			}
			catch(Exception $e){
				Xinc_Logger::getInstance()->error("undefined task '".$task->getName()."'");
				throw new Xinc_Exception_MalformedConfig();
			}
			foreach($task->attributes() as $a=>$b) {
				$setter = "set$a";
				$taskObject->$setter($b);
			}

				
			$this->handleElements($task,$taskObject);
	  	
			$project->registerTask($taskObject);


			if(!$taskObject->validate()){
					
				throw new Xinc_Exception_MalformedConfig("Error validating config.xml for task: ".$taskObject->getName());
					
					
			}


		}
	}
}