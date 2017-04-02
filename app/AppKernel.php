<?php
/*
 * Copyright (c) 2017, Josef Kufner  <josef@kufner.cz>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

/**
 * Application kernel
 */
class AppKernel extends \Symfony\Component\HttpKernel\Kernel
{

	public function __construct($environment = null, $debug = null)
	{
		if ($environment === null && !file_exists($this->getRootDir().'/config.local-prod.yml')) {
			return parent::__construct('dev', true);
		} else {
			return parent::__construct($environment ? : 'prod', $debug === null ? $environment == 'dev' : false);
		}
	}


	public function registerBundles()
	{
		return [
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
		];
	}


	public function registerContainerConfiguration(\Symfony\Component\Config\Loader\LoaderInterface $loader)
	{
		$loader->load($this->getRootDir().'/app/config/config.'.$this->getEnvironment().'.yml');
	}


	public function getRootDir()
	{
		return dirname(__DIR__);
	}


	public function getCacheDir()
	{
		return dirname(__DIR__).'/var/cache';
	}


	public function getLogDir()
	{
		return dirname(__DIR__).'/var/logs';
	}


	protected function getKernelParameters()
	{
		$param = parent::getKernelParameters();
		$param['kernel.hostname'] = $_SERVER['SERVER_NAME'];
		return $param;
	}

}

