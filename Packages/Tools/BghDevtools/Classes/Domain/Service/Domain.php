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
 * Domain definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Domain
{

	/**
	 * @var \F3\FLOW3\Object\ObjectManagerInterface
	 */
	protected $objectManager;
	
	/**
	 * @var string
	 */
	protected $namespace;
	
	/**
	 * Returns the namespace
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}
	
	/**
	 * Exceptions
	 * @var array(string=>\F3\BghDevtools\Domain\Service\DomainExceptionDefinition)
	 */
	protected $exceptions = array();
	
	/**
	 * Returns the exceptions
	 * @return array(string=>\F3\BghDevtools\Domain\Service\DomainExceptionDefinition)
	 */
	public function getExceptions()
	{
		return $this->exceptions;
	}
	
	/**
	 * services
	 * @var array(string=>\F3\BghDevtools\Domain\Service\DomainService)
	 */
	protected $services = array();
	
	/**
	 * Returns the services
	 * @return array(string=>\F3\BghDevtools\Domain\Service\DomainService)
	 */
	public function getServices()
	{
		return $this->services;
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
	 * Parse it
	 * 
	 * @param \SimpleXmlNode $node
	 * 
	 * @throws \F3\BghDevtools\Domain\Service\Exception
	 */
	public function parse(\SimpleXmlNode $node)
	{
		$attrs = $node->attributes();
		if (!isset($attrs['namespace']))
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("The domain definition needs at least a 'namespace' attribute", 1286869422);
		}
		$this->namespace = (string)$attrs['namespace'];
		if (count($attrs) > 1)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("The domain definition contains illegal attributes", 1286869423);
		}
		
		try
		{
			foreach ($node->children() as $child)
			{
				/* @var $child \SimpleXmlElement */
				switch ($child->getName())
				{
					case 'exception':
						$exception = $this->objectManager->create('F3\BghDevtools\Domain\Service\DomainExceptionDefinition');
						$exception->parse($child);
						if (isset($this->exceptions[$exception->getName()]))
						{
							throw new \F3\BghDevtools\Domain\Service\Exception("Duplicate exception definition '".$exception->getName()."'", 1286869426);
						}
						$this->exceptions[$exception->getName()] = $exception;
						break;
					case 'service':
						$service = $this->objectManager->create('F3\BghDevtools\Domain\Service\DomainService');
						$service->parse($child);
						if (isset($this->services[$service->getName()]))
						{
							throw new \F3\BghDevtools\Domain\Service\Exception("Duplicate service definition '".$service->getName()."'", 1286869427);
						}
						$this->services[$service->getName()] = $service;
						break;
					default:
						throw new \F3\BghDevtools\Domain\Service\Exception("The domain definition contains illegal nodes: ".$child->getName(), 1286869424);
				}
			}
		}
		catch (\F3\BghDevtools\Domain\Service\Exception $e)
		{
			throw new \F3\BghDevtools\Domain\Service\Exception("Error reading domain '".$this->namespace."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286869425);
		}
	}
	
}
