<?php

if (!getenv('FLOW3_CONTEXT')) putenv('FLOW3_CONTEXT=Development');
if (!getenv('FLOW3_ROOTPATH')) putenv('FLOW3_ROOTPATH='.realpath(dirname(__DIR__).'/com_bghosting_flow3_devfwkflow3'));
if (!getenv('FLOW3_WEBPATH')) putenv('FLOW3_WEBPATH='.realpath(__DIR__.'/Web'));
if (!getenv('FLOW3_DEBUG')) putenv('FLOW3_DEBUG=true');
if (!getenv('FLOW3_WORKSPACE_ROOT')) putenv('FLOW3_WORKSPACE_ROOT='.realpath(dirname(__DIR__)));
if (!getenv('FLOW3_PROJECT_NAME')) putenv('FLOW3_PROJECT_NAME=com_bghosting_flow3_devtools');
if (!getenv('FLOW3_PROJECT_DEPENDENCIES')) putenv('FLOW3_PROJECT_DEPENDENCIES=');

define('FLOW3_CONTEXT', getenv('FLOW3_CONTEXT'));
define('FLOW3_ROOTPATH', getenv('FLOW3_ROOTPATH'));
define('FLOW3_WEBPATH', getenv('FLOW3_WEBPATH'));
define('FLOW3_DEBUG', getenv('FLOW3_DEBUG'));

$_SERVER['FLOW3_CONTEXT'] = getenv('FLOW3_CONTEXT');
$_SERVER['FLOW3_ROOTPATH'] = getenv('FLOW3_ROOTPATH');
$_SERVER['FLOW3_WEBPATH'] = getenv('FLOW3_WEBPATH');
$_SERVER['FLOW3_DEBUG'] = getenv('FLOW3_DEBUG');

$Bootstrap = 'Packages/Framework/FLOW3/Scripts/FLOW3.php';
$Package = 'BghDevtools';
$Controller = 'CliStandard';
$Action = 'help';

if ($_SERVER['argc'] >= 3)
{
	switch ($_SERVER['argv'][1])
	{
		case 'model':
			$Controller = 'CliModel';
			$Action = $_SERVER['argv'][2];
			break;
		case 'xml':
			$Controller = 'CliXml';
			$Action = $_SERVER['argv'][2];
			break;
		case 'service':
			$Controller = 'CliService';
			$Action = $_SERVER['argv'][2];
			break;
		case 'project':
			$Controller = 'CliProject';
			$Action = $_SERVER['argv'][2];
			break;
	}
}

$args = array(
	__DIR__."/$Bootstrap",
	$Package,
	$Controller,
	$Action);
foreach (array_slice($_SERVER['argv'], 3) as $a) $args[] = $a;
$_SERVER['argc'] = count($args);
$_SERVER['argv'] = $args;

require_once FLOW3_ROOTPATH.'/BghFwk/utility.php';

require_once (FLOW3_ROOTPATH."/$Bootstrap");


