<?php namespace XenforoBridge\Visitor;

use XenforoBridge\Contracts\VisitorInterface;
use XenForo_Visitor;


class Visitor implements VisitorInterface
{
	
	public function getCurrentVisitor()
	{
		return XenForo_Visitor::getInstance();
	}

	public function isBanned()
	{
		return $this->getCurrentVisitor()->toArray()['is_banned'];
	}

	public function isAdmin()
	{
		return $this->getCurrentVisitor->toArray()['is_admin'];
	}
	
	public function isSuperAdmin()
	{
		return $this->getCurrentVisitor()->isSuperAdmin();
	}

	public function isLoggedIn()
	{
		return $this->getCurrentVisitor()->getUserId();
	}
}
