<?php
/**
 * @package     Joomlager Router
 *
 * @copyright   Copyright (C) 2017 Hannes Papenberg. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class PlgSystemJlrouter extends JPlugin
{
	public function construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		$app = JFactory::getApplication();

		if ($app->isAdmin())
		{
			return true;
		}

		require_once dirname(__FILE__) . '/classes/site.php';
		$router = $app->getRouter();
		$menu = $app->getMenu();

		if ($this->params->get('router_contact') || $this->params->get('router_content') || $this->params->get('router_newsfeed'))
		{
			require_once dirname(__FILE__) . '/classes/router/viewconfiguration.php';
			require_once dirname(__FILE__) . '/classes/router/view.php';
			require_once dirname(__FILE__) . '/classes/router/rules/interface.php';
			require_once dirname(__FILE__) . '/classes/router/rules/menu.php';
			require_once dirname(__FILE__) . '/classes/router/rules/nomenu.php';
			require_once dirname(__FILE__) . '/classes/router/rules/standard.php';
		}

		if ($this->params->get('router_contact'))
		{
			require_once dirname(__FILE__) . '/classes/contactrouter.php';
			$crouter = new ContactRouter($app, $menu);
			$crouter->noIDs = $this->params->get('contact_noids');
			$router->setComponentRouter('com_contact', $crouter);
		}

		if ($this->params->get('router_content'))
		{
			require_once dirname(__FILE__) . '/classes/contentrouter.php';
			$crouter = new ContentRouter($app, $menu);
			$crouter->noIDs = $this->params->get('content_noids');
			$router->setComponentRouter('com_content', $crouter);
		}

		if ($this->params->get('router_newsfeed'))
		{
			require_once dirname(__FILE__) . '/classes/newsfeedrouter.php';
			$crouter = new NewsfeedsRouter($app, $menu);
			$crouter->noIDs = $this->params->get('newsfeed_noids');
			$router->setComponentRouter('com_newsfeeds', $crouter);
		}

		if ($this->params->get('404handling') == '1')
		{
			$router->handling404 = array($this, 'modern404handling');
		}

		if ($this->params->get('404handling') == '2')
		{
			$router->handling404 = array($this, 'strict404handling');
		}
	}

	public function modern404handling($router, $vars, $uri)
	{
		if (strlen($uri->getPath()) > 0)
		{
			if (isset($vars['option']) && is_a($router->getComponentRouter($vars['option']), 'JComponentRouterView'))
			{
				throw new Exception('URL invalid', 404);
			}
		}
	}

	public function strict404handling($router, $vars, $uri)
	{
		if (strlen($uri->getPath()) > 0)
		{
			throw new Exception('URL invalid', 404);
		}
	}
}
