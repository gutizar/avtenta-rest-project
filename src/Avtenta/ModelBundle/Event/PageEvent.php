<?php

namespace Avtenta\ModelBundle\Event;

use Avtenta\ModelBundle\Entity\Page;
use Symfony\Component\EventDispatcher\Event;

class PageEvent extends Event
{
	private $page;

	public function __construct(Page $page)
	{
		$this->page = $page;
	}

	/**
	 * Get the event page
	 * @return Page
	 */
	public function getPage()
	{
		return $this->page;
	}
}