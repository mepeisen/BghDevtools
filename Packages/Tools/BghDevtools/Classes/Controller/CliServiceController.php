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
 * Controller for the service generator
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CliServiceController extends \F3\FLOW3\MVC\Controller\ActionController
{

	/**
	 * @var \F3\BghDevtools\Service\ServiceGeneratorService
	 * @inject
	 */
	protected $generatorService;
	
	/**
	 * @var array
	 */
	protected $supportedRequestTypes = array('F3\FLOW3\MVC\CLI\Request');
	
	/**
	 * Index action - displays a help message.
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$this->helpAction();
	}

	/**
	 * Help action - displays a help message
	 *
	 * @return void
	 */
	public function helpAction()
	{
		$this->response->appendContent(
			'Bghosting developer tools: Service facade generator' . PHP_EOL .
			'Usage:' . PHP_EOL .
			' php Tooling.php service <action> [<args>]' . PHP_EOL .
			PHP_EOL .
			' available actions' . PHP_EOL .
			'   generate       generates services from service xml file' . PHP_EOL .
			'                  arguments: --xml <xml-service-file>' . PHP_EOL .
			'                  example: php Tooling.php service generate --xml my-xml-file.xml' . PHP_EOL . 
			'                  note: the xml must be located at a special location.' . PHP_EOL
		);
	}

	/**
	 * Generate the whole service of a library using a service xml file.
	 *
	 * @param string $xml The name of the xml file
	 * 
	 * @return void
	 */
	public function generateAction($xml)
	{
		$basePath = dirname(dirname($xml));
		if (!is_file($xml) || !is_readable($xml))
		{
			$this->response->appendContent('ERROR: Xml file "'.$xml.'" not valid or not readable!' . PHP_EOL);
			return;
		}
		
		$file = $this->objectManager->create('F3\BghDevtools\Domain\Service\ServiceFile');
		$file->loadXml($xml);
		
		// TODO Validating class type references (parent classes, factories etc.)
		
		foreach ($file->getDomains() as $domain)
		{
			/* @var $domain \F3\BghDevtools\Domain\Service\Domain */
			$this->response->appendContent("generating service domain '".$domain->getNamespace()."'" . PHP_EOL);
			
			foreach ($domain->getExceptions() as $exception)
			{
				/* @var $exception \F3\BghDevtools\Domain\Service\DomainException */
				if (!$exception->isGenerated()) continue;
				$this->response->appendContent("   ...generating exception '".$exception->getName()."'" . PHP_EOL);
				$this->generatorService->generateExceptionClass(
					$file->getPackage(),
					'',
					$domain->getNamespace(),
					$basePath,
					$exception);
			}
			
			foreach ($domain->getServices() as $service)
			{
				/* @var $service \F3\BghDevtools\Domain\Service\DomainService */
				if (!$service->isGenerated()) continue;
				$this->response->appendContent("   ...generating service '".$service->getName()."'" . PHP_EOL);
				$this->generatorService->generateServiceClass(
					$file->getPackage(),
					'',
					$domain->getNamespace(),
					$basePath,
					$service);
			}
		}
		
		$this->response->appendContent('Done' . PHP_EOL);
	}
	
}
