<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\ViewHelpers;

/*                                                                        *
 * This script belongs to the FLOW3 package "BghDevtools".              *
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
 *                                                                        */

/**
 * view helper to normalize the type for phpdoc
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class NormalizeTypeBeanViewHelper extends \F3\Fluid\Core\ViewHelper\AbstractViewHelper
{

	/**
	 * @param string $type
	 */
	public function render($type)
	{
		$packageKey = $this->templateVariableContainer['packageKey'];
		$subpackage = $this->templateVariableContainer['subpackage'];
		$namespace = $this->templateVariableContainer['namespace'];
		$packageKey = $this->templateVariableContainer['packageKey'];
		
		switch (strtolower($type))
		{
			case 'string':
			case 'id':
				return 'string';
			case 'bool':
			case 'boolean':
				return 'boolean';
			case 'int':
			case 'integer':
				return 'integer';
			case 'double':
			case 'float':
				return 'float';
			case 'array':
				return 'array';
			case 'mixed':
			case 'undefined':
				return 'mixed';
		}
		$result = \F3\BghDevtools\ViewHelpers\NormalizeTypeViewHelper::normalizeEntityClassName($packageKey, $subpackage, $namespace, $type, '');
		return substr($result, 0, -9) . 'BeanInterface';
	}
	
}
