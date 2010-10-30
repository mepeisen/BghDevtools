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
 * Repository method definition
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class DomainRepositoryMethod extends \F3\BghDevtools\Domain\NamedElement
{
	
	/**
	 * @var string
	 */
	protected $type = false;
	
	/**
	 * Returns the method type; see type constants in this class
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @var \F3\BghDevtools\Domain\Model\DomainRepositoryMethodReturn
	 */
	protected $return = false;
	
	/**
	 * Returns the method return definition
	 * 
	 * @return \F3\BghDevtools\Domain\Model\DomainRepositoryMethodReturn
	 */
	public function getReturn()
	{
		return $this->return;
	}
	
	/**
	 * @var array(\F3\BghDevtools\Domain\Model\DomainRepositoryMethodArg)
	 */
	protected $args = array();
	
	/**
	 * Returns the method arguments
	 * 
	 * @return array(\F3\BghDevtools\Domain\Model\DomainRepositoryMethodArg)
	 */
	public function getArgs()
	{
		return $this->args;
	}

	/**
	 * Returns the visible method arguments
	 * 
	 * @return array(\F3\BghDevtools\Domain\Model\DomainRepositoryMethodArg)
	 */
	public function getVisibleArgs()
	{
		$result = array();
		foreach ($this->args as $arg)
		{
			if ($arg->isVisible()) $result[] = $arg;
		}
		return $result;
	}
	
	/**
	 * @var array(\F3\BghDevtools\Domain\Model\DomainRepositoryMethodThrows)
	 */
	protected $throws = array();
	
	/**
	 * return the throws declarations
	 * @return array(\F3\BghDevtools\Domain\Model\DomainRepositoryMethodThrows)
	 */
	public function getThrows()
	{
		return $this->throws;
	}
	
	/**
	 * @var string
	 */
	protected $onNotFound = false;
	
	/**
	 * return the on not found declaration
	 * @return string
	 */
	public function getOnNotFound()
	{
		return $this->onNotFound;
	}
	
	/**
	 * Returns true if this is a user code method
	 * @return boolean
	 */
	public function isUserCode()
	{
		return $this->type == self::TYPE_USERCODE;
	}
	
	/**
	 * Returns true if this is a find-one-object method
	 * @return boolean
	 */
	public function isFind()
	{
		return $this->type == self::TYPE_FIND;
	}
	
	/**
	 * Returns true if this is a find all method
	 * @return boolean
	 */
	public function isFindAll()
	{
		return $this->type == self::TYPE_FINDALL;
	}
	
	/**
	 * single fetch finding one value
	 * @var string
	 */
	const TYPE_FIND = 'find';
	
	/**
	 * multiple fetch finding an array of values
	 * @var string
	 */
	const TYPE_FINDALL = 'findAll';
	
	/**
	 * complete user code
	 * @var string
	 */
	const TYPE_USERCODE = 'user';
	
	/**
	 * return null on not found
	 * @var string
	 */
	const ONNOTFOUND_NULL = 'null';
	
	/**
	 * return false on not found
	 * @var string
	 */
	const ONNOTFOUND_FALSE = 'false';
	
	/**
	 * return an empty array on not found
	 * @var string
	 */
	const ONNOTFOUND_EMPTY = 'empty';
	
	/**
	 * Returns the element name to be used in exceptions
	 * 
	 * @return string
	 */
	protected function getElementName()
	{
		return 'Repository-Method-Definition';
	}
	
	/**
	 * Applies attributes
	 * 
	 * @param string $key
	 * @param string $val
	 * 
	 * @return boolean true if the attribute is ok; false if the attribute is invalid
	 */
	protected function applyAttribute($key, $val)
	{
		if (parent::applyAttribute($key, $val)) return true;
		
		if ($key == 'type')
		{
			$this->type = $val;
			return true;
		}
		
		return false;
	}
	
	/**
	 * Applies children
	 * 
	 * @param \SimpleXmlElement $element
	 * 
	 * @return boolean true if the child is ok; false if the child is invalid
	 */
	protected function applyChild(\SimpleXmlElement $element)
	{
		if (parent::applyChild($element)) return true;
		
		if ($element->getName() == 'return')
		{
			if ($this->return !== false)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate repository method return for method '".$this->getName()."'", 1286872780);
			}
			try
			{
				$this->return = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainRepositoryMethodReturn');
				$this->return->parse($element);
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading method return for method '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286872782);
			}
			return true;
		}
		
		if ($element->getName() == 'throws')
		{
			try
			{
				$throw = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainRepositoryMethodThrows');
				$throw->parse($element);
			    $this->throws[] = $throw;
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading method throws for method '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286872783);
			}
			return true;
		}
		
		if ($element->getName() == 'onNotFound')
		{
			if ($this->onNotFound !== false)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Duplicate repository method onNotFound for method '".$this->getName()."'", 1286872781);
			}
			$this->onNotFound = (string)$element;
			return true;
		}
		
		if ($element->getName() == 'arg')
		{
			try
			{
			    $arg = $this->objectManager->create('F3\BghDevtools\Domain\Model\DomainRepositoryMethodArg');
			    $arg->parse($element);
				$this->args[] = $arg;
			}
			catch (\F3\BghDevtools\Domain\Model\Exception $e)
			{
				throw new \F3\BghDevtools\Domain\Model\Exception("Error reading method argument for method '".$this->getName()."'. Nested code: ".$e->getCode()." / Nested message: ".$e->getMessage(), 1286872783);
			}
			return true;
		}

		return false;
	}
	
	/**
	 * Validates the content
	 */
	protected function validate()
	{
		parent::validate();
		
		if ($this->type === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method '".$this->getName()."'. Missing type", 1286872784);
		}
	
		if ($this->type != self::TYPE_USERCODE && $this->onNotFound === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method '".$this->getName()."'. Missing onNotFound", 1286872785);
		}
	
		if ($this->type != self::TYPE_USERCODE && $this->return === false)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method '".$this->getName()."'. Missing return", 1286872786);
		}
		
		if ($this->type != self::TYPE_FIND && $this->type != self::TYPE_FINDALL && $this->type != self::TYPE_USERCODE)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method '".$this->getName()."'. Invalid type '".$this->type."'", 1286872787);
		}
		
		if ($this->type != self::TYPE_USERCODE && $this->onNotFound != self::ONNOTFOUND_EMPTY && $this->onNotFound != self::ONNOTFOUND_FALSE && $this->onNotFound != self::ONNOTFOUND_NULL)
		{
			throw new \F3\BghDevtools\Domain\Model\Exception("Error reading repository method '".$this->getName()."'. Invalid onNotFound '".$this->onNotFound."'", 1286872788);
		}
	}
	
}
