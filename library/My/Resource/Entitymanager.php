<?php
class My_Resource_Entitymanager extends Zend_Application_Resource_ResourceAbstract
{
	protected $_options = array(
		'connection' => array(
			'driver' => 'pdo_mysql', 
			'host' => 'localhost', 
			'dbname' => 'dbname', 
			'user' => 'root', 
			'password' => ''),
		'modelDir' => '/models',
		'proxyDir' => '/proxies',
		'proxyNamespace' => 'Proxies',
		'autoGenerateProxyClasses' => true
	);

	public function init()
	{
		$options = $this->getOptions();
		
		$config = new \Doctrine\ORM\Configuration;
		$cache = new \Doctrine\Common\Cache\ArrayCache;
		$driverImpl = $config->newDefaultAnnotationDriver($options['modelDir']);

		$config->setMetadataCacheImpl($cache);
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir($options['proxyDir']);
		$config->setProxyNamespace($options['proxyNamespace']);
		$config->setAutoGenerateProxyClasses($options['autoGenerateProxyClasses']);
		$config->setMetadataDriverImpl($driverImpl);

		$em = \Doctrine\ORM\EntityManager::create($options['connection'], $config);
		Zend_Registry::set('em', $em);
		
		return $em;
	}
}