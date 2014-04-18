<?php

namespace Avtenta\RestBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Avtenta\ModelBundle\Form\PageType;
use Avtenta\ModelBundle\Exception\InvalidFormException;

class PageController extends FOSRestController
{
	/**
	 * Get all Pages,
	 * 
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Gets all pages",
	 *   output = "Array of Avtenta\ModelBundle\Entity\Page",
	 *   statusCodes = {
	 *     200 = "Returned when successful"
	 *   }
	 * )
	 * 
	 * @Rest\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing pages")
	 * @Rest\QueryParam(name="limit", requirements="\d+", default="5", description="Number of pages to return")
	 * 
	 * @Rest\View(templateVar="pages")
	 * 
	 * @param  Request $request the request object
	 * @param  ParamFetcherInterface $paramFetcher param fetcher service
	 * 
	 * @return array
	 */
	public function getPagesAction(Request $request, ParamFetcherInterface $paramFetcher)
	{
		$offset = $paramFetcher->get('offset');
		$offset = (null == $offset) ? 0 : $offset;
		$limit = $paramFetcher->get('limit');

		return $this->get('avtenta.manager.page')->all($offset, $limit);
	}

	/**
	 * Get single Page,
	 *
	 * @ApiDoc(
	 *   resource = true,
	 *   description = "Gets a Page for a given id",
	 *   output = "Avtenta\ModelBundle\Entity\Page",
	 *   statusCodes = {
	 *     200 = "Returned when successful",
	 *     404 = "Returned when the page is not found"
	 *   }
	 * )
	 *
	 * @Rest\View(templateVar="page")
	 *
	 * @param Request $request the request object
	 * @param int     $id      the page id
	 *
	 * @return array
	 *
	 * @throws NotFoundHttpException when page not exist
	 * 
	 */
	public function getPageAction($id)
	{
		$page = $this->getOr404($id);

		return $page;
	}

	/**
	 * @param int $id the page id
	 * 
	 * @return page
	 * 
	 * @throws NotFoundHttpException when the page does not exist
	 */
	protected function getOr404($id)
	{
		if (!($page = $this->get('avtenta.manager.page')->get($id)))
		{
			throw new NotFoundHttpException(sprintf("The resource \'$id\' was not found", $id));
		}

		return $page;
	}

	/**
     * Create a Page from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new page from the submitted data.",
     *   input = "Acme\BlogBundle\Form\PageType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Rest\View(
     *  template = "AvtentaModelBundle:Page:newPage.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormType|View
     */
	public function postPageAction(Request $request)
	{
		try
		{
			$form = new PageType();

			$newPage = $this->get('avtenta.manager.page')->post(
				$request->request->get($form->getName())
			);

			$routeOptions = array(
				'id' => $newPage->getId(),
				'_format' => $request->get('_format')
			);

			$view = $this->routeRedirectView('api_1_get_page', $routeOptions, Codes::HTTP_CREATED); 
			
			return $this->handleView($view);
		}
		catch (InvalidFormException $exception)
		{
			return $exception->getForm();
		}
	}

	/**
	 * Presents the form to use to create a new page.
	 *
	 * @ApiDoc(
	 *   resource = true,
	 *   statusCodes = {
	 *     200 = "Returned when successful"
	 *   }
	 * )
	 *
	 * @Rest\View()
	 *
	 * @return FormType
	 */
	public function newPageAction()
	{
	    return $this->createForm(new PageType());
	}

	/**
	 * Update existing page from the submitted data or create a new page at a specific location.
	 *
	 * @ApiDoc(
	 *   resource = true,
	 *   input = "Avtenta\ModelBundle\Form\PageType",
	 *   statusCodes = {
	 *     201 = "Returned when the Page is created",
	 *     204 = "Returned when successful",
	 *     400 = "Returned when the form has errors"
	 *   }
	 * )
	 *
	 * @Rest\View(
	 *  template = "AvtentaModelBundle:Page:editPage.html.twig",
	 *  templateVar = "form"
	 * )
	 *
	 * @param Request $request the request object
	 * @param int     $id      the page id
	 *
	 * @return FormType|View
	 *
	 * @throws NotFoundHttpException when page not exist
	 */
	public function putPageAction(Request $request, $id)
	{
		try
		{
			if (!($page = $this->get('avtenta.manager.page')->get($id)))
			{
				$statusCode = Codes::HTTP_CREATED;
				$page = $this->get('avtenta.manager.page')->post(
					$request->request->all()
				);
			}
			else
			{
				$statusCode = Codes::HTTP_NO_CONTENT;
				$page = $this->get('avtenta.manager.page')->put(
					$page,
					$request->request->all()
				);
			}

			$routeOptions = array(
				'id' => $page->getId(),
				'_format' => $request->get('_format')
			);

			return $this->routeRedirectView('api_1_get_page', $routeOptions, $statusCode);
		}
		catch (InvalidFormException $exception)
		{
			return $exception->getForm();
		}
	}

	/**
     * Update existing page from the submitted data or create a new page at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Avtenta\ModelBundle\Form\PageType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Rest\View(
     *  template = "AvtentaModelBundle:Page:editPage.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the page id
     *
     * @return FormType|View
     *
     * @throws NotFoundHttpException when page not exist
     */
	public function patchPageAction(Request $request, $id)
	{
		try 
		{
            $page = $this->get('avtenta.manager.page')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $page->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_page', $routeOptions, Codes::HTTP_NO_CONTENT);

        } 
        catch (InvalidFormException $exception) 
        {
            return $exception->getForm();
        }
	}

	/**
     * Delete an existing page.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     404 = "Returned when the entity does not exist"
     *   }
     * )
     *
     * @Rest\View(
     *  template = "AvtentaModelBundle:Page:deletePage.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the page id
     *
     * @return FormType|View
     *
     * @throws NotFoundHttpException when page not exist
     */
	public function deletePageAction(Request $request, $id)
	{
		$page = $this->get('avtenta.manager.page')->delete(
            $this->getOr404($id)
        );

        return $this->view(null, Codes::HTTP_NO_CONTENT);
	}
}