<?php
require_once("Xinc/Plugin/Interface.php");
require_once("Plugins/ModificationSet/SvnTask.php");
require_once("Plugins/ModificationSet/ModificationSetTask.php");

require_once 'Xinc/Logger.php';
require_once 'Xinc/ModificationSet/Interface.php';
require_once 'Xinc/Exception/ModificationSet.php';

class Plugins_SvnModificationSet implements Xinc_Plugin_Interface {
	public function getTaskDefinitions(){
		return array(new Plugins_ModificationSet_SvnTask($this));
	}
	public function getFilename(){
		return __FILE__;
	}
	public function getClassname(){
		return get_class($this);
	}


	/**
	 * Checks whether the Subversion project has been modified.
	 *
	 * @return boolean
	 */
	public function checkModified($dir)
	{
		if (!file_exists($dir)) {
			throw new Xinc_Exception_ModificationSet('Subversion checkout directory not present');
		}

		$cwd = getcwd();
		chdir($dir);

		$output = '';
		$result = 9;
		exec('svn info', $output, $result);

		if ($result == 0) {
			$localSet = implode("\n",$output);

			$url = $this->getURL($localSet);
				
			$output = '';
			$result = 9;
			exec('svn info ' . $url, $output, $result);
			$remoteSet = implode("\n", $output);

			if ($result != 0) {
				throw new Xinc_Exception_ModificationSet('Problem with remote Subversion repository');
			}

			$localRevision = $this->getRevision($localSet);
			$remoteRevision = $this->getRevision($remoteSet);
				
			Xinc_Logger::getInstance()->debug("Subversion checkout dir is $dir local revision @ $localRevision Remote Revision @ $remoteRevision \n");
			chdir($cwd);
			return $localRevision < $remoteRevision;
		} else {
			var_dump($output);
			chdir($cwd);
			throw new Xinc_Exception_ModificationSet('Subversion checkout directory is not a working copy.');
		}
	}

	/**
	 * Parse the result of an svn command for the Subversion project URL.
	 *
	 * @param string $result
	 * @return string
	 */
	private function getUrl($result)
	{
		$list = split("\n",$result);
		foreach ($list as $row) {
			$field = split(": ", $row);
			if (preg_match("/URL/",$field[0])) {
				return trim($field[1]);
			}
		}
	}

	/**
	 * Parse the result of an svn command for the Subversion project revision number.
	 *
	 * @param string $result
	 * @return string
	 */
	function getRevision($result) {
		$list = split("\n",$result);
		foreach ($list as $row) {
			$field = split(":", $row);
			if (preg_match("/Revision/",$field[0])) {
				return trim($field[1]);
			}
		}
	}

	/**
	 * Check necessary variables are set
	 *
	 * @throws Xinc_Exception_MalformedConfig
	 */
	public function validate()
	{
		exec("whereis svn",$output,$result);
		$parts=split(" ",$output[0]);

		if(empty($parts[1])){
			Xinc_Logger::getInstance()->error("command 'svn' not found");
				
			return false;
		}
		return true;

	}
}
?>