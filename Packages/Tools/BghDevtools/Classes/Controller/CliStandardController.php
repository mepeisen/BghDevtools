<?php
declare(ENCODING = 'utf-8');
namespace F3\BghDevtools\Controller;

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
 * Controller for standard help
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CliStandardController extends \F3\FLOW3\MVC\Controller\ActionController
{

	/**
	 * @var array
	 */
	protected $supportedRequestTypes = array('F3\FLOW3\MVC\CLI\Request');
	
	/**
	 * Index action - displays a help message.
	 *
	 * @return void
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 */
	public function indexAction()
	{
		$this->helpAction();
	}

	/**
	 * Help action - displays a help message
	 *
	 * @return void
	 * @author Christopher Hlubek <hlubek@networkteam.com>
	 */
	public function helpAction()
	{
		$this->response->appendContent(
			'Bghosting developer tools' . PHP_EOL .
			'Usage:' . PHP_EOL .
			' php Tooling.php <type> <action> [<args>]' . PHP_EOL .
			PHP_EOL .
			' available types (use action help to see details):' . PHP_EOL .
			'   model         Modelling tools/ generating entites, repositories etc.' . PHP_EOL .
			'   service       Service tools/ generating service facades' . PHP_EOL .
			'   project       Project tools/ create and register projects' . PHP_EOL .
			'   xml           XML schema bindings' . PHP_EOL .
			PHP_EOL .
			' examples: ' . PHP_EOL .
			'   php Tooling.php model help' . PHP_EOL .
			'   php Tooling.php project create --path /my/project/path' . PHP_EOL
		);
	}
	
}
