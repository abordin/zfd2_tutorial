<?php
define('APPLICATION_PATH', __DIR__ . '/..');

set_include_path(
	implode(PATH_SEPARATOR, 
		array(
			realpath(APPLICATION_PATH . '/../library'), 
			get_include_path())));

require 'Zend/Application.php';

$application = new Zend_Application('development', APPLICATION_PATH . '/configs/application.ini');

$bootstrap = $application->getBootstrap()->bootstrap('entityManager');
$em = $bootstrap->getResource('entityManager');

$helperSet = null;
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(
	array(
		'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()), 
		'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)));

$cli = new \Symfony\Component\Console\Application('Doctrine Command Line Interface', 
	Doctrine\Common\Version::VERSION);
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);
$cli->addCommands(
	array(
		// DBAL Commands
		new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(), 
		new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(), 
		
		// ORM Commands
		new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(), 
		new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand()));

$cli->run();