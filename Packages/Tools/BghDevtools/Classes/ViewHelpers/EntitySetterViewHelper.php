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
 * view helper to calculate the name of the setter
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 */
class EntitySetterViewHelper extends \F3\Fluid\Core\ViewHelper\AbstractViewHelper
{

	/**
	 * @param \F3\BghDevtools\Domain\Model\DomainEntityAttribute $attribute
	 */
	public function render(\F3\BghDevtools\Domain\Model\DomainEntityAttribute $attribute)
	{
		return 'set'.ucfirst($attribute->getName());
	}
	
}
