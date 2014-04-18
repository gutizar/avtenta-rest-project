<?php

namespace Avtenta\ModelBundle\Tests\Manager;

use Avtenta\ModelBundle\Manager\PageManager;
use Avtenta\ModelBundle\Entity\Page;

class PageManagerTest extends \PHPUnit_Framework_TestCase
{
	const PAGE_CLASS = 'Avtenta\ModelBundle\Tests\Manager\DummyPage';

	/**
	 * @var PageManager
	 */
	protected $pageManager;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $om;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactory;

	public function setUp()
	{
		if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }

        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::PAGE_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::PAGE_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::PAGE_CLASS));
	}

	public function testGet()
    {
        $id = 1;
        $page = $this->getPage();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($page));

        $this->pageManager = $this->createPageManager($this->om, static::PAGE_CLASS, $this->formFactory);

        $this->pageManager->get($id);
    }

    public function testPost()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $page = $this->getPage();
        $page->setTitle($title);
        $page->setBody($body);

        $form = $this->getMock('Avtenta\ModelBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($page));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->pageManager = $this->createPageManager($this->om, static::PAGE_CLASS,  $this->formFactory);
        $pageObject = $this->pageManager->post($parameters);

        $this->assertEquals($pageObject, $page);
    }

    /**
     * @expectedException Avtenta\ModelBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $page = $this->getPage();
        $page->setTitle($title);
        $page->setBody($body);

        $form = $this->getMock('Avtenta\ModelBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->pageManager = $this->createPageManager($this->om, static::PAGE_CLASS,  $this->formFactory);
        $this->pageManager->post($parameters);
    }

    protected function createPageManager($objectManager, $pageClass, $formFactory)
    {
        return new PageManager($objectManager, $pageClass, $formFactory);
    }

    protected function getPage()
    {
        $pageClass = static::PAGE_CLASS;

        return new $pageClass();
    }
}

class DummyPage extends Page
{
}