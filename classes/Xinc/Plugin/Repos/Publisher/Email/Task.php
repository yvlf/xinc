<?php
/**
 * This interface represents a publishing mechanism to publish build results
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
require_once 'Xinc/Plugin/Repos/Publisher/AbstractTask.php';

class Xinc_Plugin_Repos_Publisher_Email_Task extends Xinc_Plugin_Repos_Publisher_AbstractTask
{
   
    private $_to;
    private $_subject;
    private $_message;
    public function getName()
    {
        return 'email';
    }
    
    /**
     * Set the email address to send to
     *
     * @param string $subject
     */
    public function setTo($to)
    {
        $this->_to = (string)$to;
    }
    
    /**
     * Set the subject of the email
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->_subject = (string)$subject;
    }
    
    /**
     * Set the message of the email
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->_message = (string)$message;
    }
    
    public function validateTask()
    {
        
        if (!isset($this->_to)) {
              throw new Xinc_Exception_MalformedConfig('Element publisher/email - required attribute '
                                                      .'\'to\' is not set');
        }
        if (!isset($this->_subject)) {
            throw new Xinc_Exception_MalformedConfig('Element publisher/email - required attribute '
                                                    .'\'subject\' is not set');
        }
        if (!isset($this->_message)) {
            throw new Xinc_Exception_MalformedConfig('Element publisher/email - required attribute '
                                                    .'\'message\' is not set');
        }
        return true;
    }
    
    public function publish(Xinc_Project &$project)
    {
        $statusBefore = $project->getStatus();
        $res = $this->_plugin->email($project, $this->_to, $this->_subject, $this->_message);
        if (!$res && $statusBefore == Xinc_Project_Build_Status_Interface::PASSED ) {
            /**
             * Status was PASSED, but now the publish process made it fail
             */
            $project->setStatus(Xinc_Project_Build_Status_Interface::FAILED);
        }
    }
}