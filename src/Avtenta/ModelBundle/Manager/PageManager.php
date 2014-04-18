<?php

namespace Avtenta\ModelBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Avtenta\ModelBundle\Entity\Page;
use Avtenta\ModelBundle\Form\PageType;
use Avtenta\ModelBundle\Exception\InvalidFormException;

class PageManager
{
	private $class;
	private $repository;
	private $objectManager;
	private $formFactory;

	/**
	 * 
	 * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
	 * @param string $class
	 */
	public function __construct(ObjectManager $objectManager, $class, FormFactoryInterface $formFactory)
	{
		$this->class = $class;
		$this->objectManager = $objectManager;
		$this->repository = $objectManager->getRepository($class);
		$this->formFactory = $formFactory;
	}

	/**
	 * Lists all the pages.
	 * 
	 * @param int $offset
	 * @param int $limit
	 * @return array array of pages
	 */
	public function all($offset, $limit)
	{
		return $this->repository->findBy(array(), null, $limit, $offset);
	}

	/**
	 * Retrieves the page for the given ID.
	 * 
	 * @param int $id
	 * @return Avtenta\ModelBundle\Entity\Page page with ID $id
	 */
	public function get($id)
	{
		return $this->repository->find($id);
	}

	/**
	 * Creates a new Page.
	 * 
	 * @param  array  $parameters Request parameters
	 * @return Avtenta\ModelBundle\Entity\Page
	 */
	public function post(array $parameters)
	{
		$page = $this->createPage();

		return $this->processForm($page, $parameters, 'POST');
	}

	/**
	 * Edits a page, or creates it if it does not exist.
	 * 
	 * @param  Page   $page
	 * @param  array  $parameters
	 * @return Avtenta\ModelBundle\Entity\Page
	 */
	public function put(Page $page, array $parameters)
	{
		return $this->processForm($page, $parameters, 'PUT');
	}

	/**
	 * Partially updates a Page.
	 * 
	 * @param  Page   $page
	 * @param  array  $parameters
	 * @return Avtenta\ModelBundle\Entity\Page
	 */
	public function patch(Page $page, array $parameters)
	{
		return $this->processForm($page, $parameters, 'PATCH');
	}

	/**
	 * Updates an existing Page.
	 * 
	 * @param Avtenta\ModelBundle\Entity\Page $entity
	 * @param bool $doFlush
	 */
	public function update($entity, $doFlush = true)
	{
		$this->objectManager->persist($entity);

		if ($doFlush)
		{
			$this->objectManager->flush();
		}
	}

	/**
	 * Deletes a Page.
	 * 
	 * @param Avtenta\ModelBundle\Entity\Page $entity
	 * @param bool $doFlush
	 */
	public function delete($entity, $doFlush = true)
	{
		$this->objectManager->remove($entity);

		if ($doFlush)
		{
			$this->objectManager->flush();
		}
	}

	/**
	 * Process the form.
	 * 
	 * @param  Page   $page
	 * @param  array  $parameters
	 * @param  string $method
	 * 
	 * @return Page
	 * 
	 * @throws \Avtenta\ModelBundle\Exception\InvalidFormException
	 */
	private function processForm(Page $page, array $parameters, $method = 'PUT')
	{
		$form = $this->formFactory->create(new PageType(), $page, array('method' => $method));
		$form->submit($parameters, 'PATCH' !== $method);

		if ($form->isValid())
		{
			$page = $form->getData();
			$this->update($page, true);

			return $page;
		}

		throw new InvalidFormException("Invalid submitted data", $form);
	}

	/**
	 * Creates a new instance of the Page entity.
	 * 
	 * @return Avtenta\ModelBundle\Entity\Page
	 */
	private function createPage()
	{
		return new $this->class;
	}
}