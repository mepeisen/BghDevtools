<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Domain\Service;

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
 * File representation of a service file
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ServiceFile
{

	/**
	 * @var \F3\FLOW3\Object\ObjectManagerInterface
	 */
	protected $objectManager;
	
	/**
	 * The domains
	 * @var array(string=>\F3\BghDevtools\Domain\Service\Domain)
	 */
	protected $domains = array();
	
	/**
	 * The package
	 * @var string
	 */
	protected $package;
	
	/**
	 * Returns the package key
	 * 
	 * @return string
	 */
	public function getPackage()
	{
		return $this->package;
	}
	
	/**
	 * Returns the domains
	 * 
	 * @return array(string=>\F3\BghDevtools\Domain\Service\Domain)
	 */
	public function getDomains()
	{
		return $this->domains;
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
			throw new \F3\BghDevtools\Domain\Service\Exception("Problems loading xml file '$xmlPath'", 1286869421);
		}
		
		$attrs = $xml->attributes();
		if (!isset($attrs['package']))
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Problems loading xml file '$xmlPath'. Missing package", 1286869422);
		}
		if (count($attrs) != 1)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Problems loading xml file '$xmlPath'. Illegal attributes", 1286869423);
		}
		$this->package = (string)$attrs['package'];
		
		foreach ($xml->children() as $child)
		{
			/* @var $child \SimpleXmlElement */
			if ($child->getName() != 'domain') throw new \F3\BghDevtools\Domain\Service\Exception("Error reading xml file. Unknown root element.", 1286873579);
			$domain = $this->objectManager->create('F3\BghDevtools\Domain\Service\Domain');
			$domain->parse($child);
			if (isset($this->domains[$domain->getNamespace()])) throw new \F3\BghDevtools\Domain\Service\Exception("Error reading xml file. Duplicate domain '".$domain->getNamespace()."'", 1286873580);
			$this->domains[$domain->getNamespace()] = $domain;
		}
	}
	
}
