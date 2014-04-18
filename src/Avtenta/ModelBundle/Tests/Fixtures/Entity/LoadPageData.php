<?php

namespace Avtenta\ModelBundle\Tests\Fixtures\Entity;

use Avtenta\ModelBundle\Entity\Page;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPageData implements FixtureInterface
{
    static public $pages = array();

    public function load(ObjectManager $manager)
    {
        $page = new Page();
        $page->setTitle('title');
        $page->setBody('body');

        $manager->persist($page);
        $manager->flush();

        self::$pages[] = $page;
    }
}