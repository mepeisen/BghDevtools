<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Domain\Model;

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
 * @scope prototype
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
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainExceptionDefinition)
	 */
	protected $exceptions = array();
	
	/**
	 * Returns the exceptions
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainExceptionDefinition)
	 */
	public function getExceptions()
	{
		return $this->exceptions;
	}
	
	/**
	 * Repositories
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainRepository)
	 */
	protected $repositories = array();
	
	/**
	 * Returns the repositories
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainRepository)
	 */
	public function getRepositories()
	{
		return $this->repositories;
	}
	
	/**
	 * Entities
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainEntity)
	 */
	protected $entities = array();
	
	/**
	 * Returns the entities
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainEntity)
	 */
	public function getEntities()
	{
		return $this->entities;
	}
	
	/**
	 * Factories
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainFactory)
	 */
	protected $factories = array();
		
	/**
	 * Returns the factories
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainFactory)
	 */
	public function getFactories()
	{
		return $this->factories;
	}
	
	/**
	 * Aspects
	 * @var array(string=>\F3\BghDevtools\Domain\Model\DomainAspect)
	 */
	protected $aspects = array();
		
	/**
	 * Returns the aspects
	 * @return array(string=>\F3\BghDevtools\Domain\Model\DomainAspect)
	 */
	public function getAspects()
	{
		return $this->aspects;
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
	 * parse it
	 * 
	 * @param \SimpleXmlElement $node
	 * 
	 * @throws \F3\BghDevtools\Domain\Model\Exception
	 */
	public function parse(\SimpleXmlElement $node)
	{
		$attrs = $node->attributes();
		if (!isset($attrs['namespace']))
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("The domain definition needs at least a 'namespace' attribute", 1286869422);
		}
		$this->namespace = (string)$attrs['namespace'];
		if (count($attrs) > 1)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("The domain definition contains illegal attributes", 1286869423);
		}
		
		try
		{
			foreach ($node->children() as $child)
			{
				/* @var $child \SimpleXmlElement */
				switch ($child->getName())
				{
					case 'exception':
						$exception = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainExceptionDefinition');
						$exception->parse($child);
						if (isset($this->exceptions[$exception->getName()]))
						{
							throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate exception definition '".$exception->getName()."'", 1286869426);
						}
						$this->exceptions[$exception->getName()] = $exception;
						break;
					case 'repository':
						$repository = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainRepository');
						$repository->parse($child);
						if (isset($this->repositories[$repository->getName()]))
						{
							throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate repository definition '".$repository->getName()."'", 1286869427);
						}
						$this->repositories[$repository->getName()] = $repository;
						break;
					case 'entity':
						$entity = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainEntity');
						$entity->parse($child);
						if (isset($this->entities[$entity->getName()]))
						{
							throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate entity definition '".$entity->getName()."'", 1286869428);
						}
						$this->entities[$entity->getName()] = $entity;
						break;
					case 'factory':
						$factory = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainFactory');
						$factory->parse($child);
						if (isset($this->factories[$factory->getName()]))
						{
							throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate factory definition '".$factory->getName()."'", 1286869429);
						}
						$this->factories[$factory->getName()] = $factory;
						break;
					case 'aspect':
						$aspect = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainAspect');
						$aspect->parse($child);
						if (isset($this->aspects[$aspect->getName()]))
						{
							throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate aspect definition '".$aspect->getName()."'", 1286869430);
						}
						$this->aspects[$aspect->getName()] = $aspect;
						break;
					default:
						throw new \F3\BghDevtools\Domain\Model\Exception("The domain definition contains illegal nodes: ".$child->getName(), 1286869424);
				}
			}
		}
		catch (\F3\BghDevtools\Domain\Model\Exception $e)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading domain '".$this->namespace."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286869425);
		}
	}
	
}
