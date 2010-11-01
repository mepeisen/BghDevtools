<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Domain\Project;

/*                                                                        *
 * This script belongs to the FLOW3 package "BghDevtools"                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * File representation of a project file
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class ProjectFile
{

	/**
	 * @var \F3\FLOW3\Object\ObjectManagerInterface
	 */
	protected $objectManager;
	
	/**
	 * The packages
	 * @var array(string=>\F3\BghDevtools\Domain\Project\Package)
	 */
	protected $packages = array();
	
	/**
	 * The dependencies
	 * @var array(string=>\F3\BghDevtools\Domain\Project\Dependency)
	 */
	protected $dependencies = array();
	
	/**
	 * Returns the packages
	 * 
	 * @return array(string=>\F3\BghDevtools\Domain\Project\Package)
	 */
	public function getPackages()
	{
		return $this->packages;
	}
	
	/**
	 * Returns the dependencies
	 * 
	 * @return array(string=>\F3\BghDevtools\Domain\Project\Dependency)
	 */
	public function getDependencies()
	{
		return $this->dependencies;
	}
	
	/**
	 * Constructor
	 * 
	 * @param \F3\FLOW3\Object\ObjectManagerInterface $objectManager
	 */
	public function __construct(\F3\FLOW3\Object\ObjectManagerInterface $objectManager)
	{
		$this->objectManager = $objectManager;
	}
	
	/**
	 * Loads an xml file
	 * 
	 * @param string $xmlPath
	 */
	public function loadXml($xmlPath)
	{
		$xml = @simplexml_load_file($xmlPath);
		if (!$xml)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Problems loading xml file '$xmlPath'", 1286869421);
		}
		
		$attrs = $xml->attributes();
		if (count($attrs) != 0)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Problems loading xml file '$xmlPath'. Illegal attributes", 1286869423);
		}
		
		foreach ($xml->children() as $child)
		{
			/* @var $child \SimpleXmlElement */
		    switch ($child->getName())
		    {
		        case 'package':
        			$pkg = $this->objectManager->create('F3\BghDevtools\Domain\Project\Package');
        			$pkg->parse($child);
        			if (isset($this->packages[$pkg->getName()])) throw new \F3\BghDevtools\Domain\Model\Exception("Error reading xml file. Duplicate package '".$pkg->getName()."'", 1286873580);
        			$this->packages[$pkg->getName()] = $pkg;
		            break;
		        case 'dependency':
        			$dep = $this->objectManager->create('F3\BghDevtools\Domain\Project\Dependency');
        			$dep->parse($child);
        			if (isset($this->dependencies[$dep->getProject()])) throw new \F3\BghDevtools\Domain\Model\Exception("Error reading xml file. Duplicate dependency '".$dep->getProject()."'", 1286873580);
        			$this->dependencies[$dep->getProject()] = $dep;
		            break;
		        default:
		            throw new \F3\BghDevtools\Domain\Model\Exception("Error reading xml file. Unknown root element. ".$child->getName(), 1286873579);
		    }
		}
	}
	
}
