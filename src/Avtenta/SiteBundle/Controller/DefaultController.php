<?php

namespace Avtenta\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Avtenta\ModelBundle\Entity\Page;
use Avtenta\ModelBundle\Form\PageType;

use Fetch\Server;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * [showAction description]
     * 
     * @Route("/page/{id}", requirements={"id" = "\d+"}, name="site_page_show")
     * @Method("GET")
     * @Template("AvtentaSiteBundle:Default:show.html.twig")
     * 
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function showAction($id)
    {
    	$em = $this->getDoctrine()->getManager();

    	$page = $em->getRepository('AvtentaModelBundle:Page')->find($id);

    	if (!$page) 
    	{
	    throw $this->createNotFoundException('Unable to find the page entity');
    	}

    	return array(
	    'page' => $page
    	);
    }

    /**
     * [newAction description]
     * 
     * @Route("/page/new", name="site_page_new")
     * @Template("AvtentaSiteBundle:Default:new.html.twig")
     * 
     * @return [type] [description]
     */
    public function newAction()
    {
    	$page = new Page();
    	$form = $this->createPageForm($page, 'site_page_create');

    	return array(
	    'page' => $page,
	    'form' => $form->createView()
    	);
    }

	/**
	 * [createAction description]
	 * 
	 * @Route("/page", name="site_page_create")
	 * @Method("POST")
	 * @Template("AvtentaSiteBundle:Default.new.html.twig")
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function createAction(Request $request)
	{
	    $em = $this->getDoctrine()->getManager();

	    $page = new Page();
	    $form = $this->createPageForm($page, 'site_page_create');
	    $form->handleRequest($request);

	    if ($form->isValid())
	    {
		$em->persist($page);
		$em->flush();

		return $this->redirect($this->generateUrl('site_page_show', array('id' => $page->getId())));
	    }

	    return array(
		'page' => $page,
		'form' => $form->createView()
	    );
	}

	/**
	 * [editAction description]
	 * 
	 * @Route("/page/{id}/edit", name="site_page_edit")
	 * @Method("GET")
	 * @Template("AvtentaSiteBundle:Default:edit.html.twig")
	 * 
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function editAction($id)
	{
	    $em = $this->getDoctrine()->getManager();

	    $page = $em->getRepository('AvtentaModelBundle:Page')->find($id);

	    if (!$page)
	    {
		$this->createNotFoundException('Unable to find the page entity');
	    }

	    $editForm = $this->createPageForm($page, 'site_page_update', array('id' => $page->getId()), 'PUT');

	    return array(
		'page' => $page,
		'edit_form' => $editForm->createView()
	    );
	}

	/**
	 * [updateAction description]
	 * 
	 * @Route("/page/{id}", name="site_page_update")
	 * @Method("PUT")
	 * @Template("AvtentaSiteBundle:Default:edit.html.twig")
	 * 
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function updateAction(Request $request, $id)
	{
	    $em = $this->getDoctrine()->getManager();

	    $page = $em->getRepository('AvtentaModelBundle:Page')->find($id);

	    if (!$page)
	    {
		$this->createNotFoundException('Unable to find the page entity');
	    }

	    $editForm = $this->createPageForm($page, 'site_page_update', array('id' => $page->getId()), 'PUT');
	    $editForm->handleRequest($request);

	    if ($editForm->isValid())
	    {
		$em->flush();

		return $this->redirect($this->generateUrl('site_page_show', array('id' => $page->getId())));
	    }

	    return array(
		'page' => $page,
		'edit_form' => $editForm->createView()
	    );
	}

	/**
	 * [patchAction description]
	 * 
	 * @Route("/page/{id}", name="site_page_patch")
	 * @Method("PATCH")
	 * @Template("AvtentaSiteBundle:Default:edit.html.twig")
	 * 
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function patchAction(Request $request, $id)
	{
	    $em = $this->getDoctrine()->getManager();

	    $page = $em->getRepository('AvtentaModelBundle:Page')->find($id);

	    if (!$page)
	    {
		$this->createNotFoundException('Unable to find the page entity');
	    }

	    $editForm = $this->createPageForm($page, 'site_page_patch', array('id' => $page->getId()), 'PATCH');
	    $editForm->handleRequest($request);

	    if ($editForm->isValid())
	    {
		$em->flush();

		if ($request->isXmlHttpRequest())
		{
		    $json = json_encode(array(
			'sucess' => true,
			'page' => $page
		    ));

		    $response = new Response($json);
		    $response->headers->set('Content-Type', 'application/json');

		    return $response;
		}

		return $this->redirect($this->generateUrl('site_page_show', array('id' => $page->getId())));
	    }

	    return array(
		    'page' => $page,
		    'edit_form' => $editForm->createView()
	    );
	}

	private function createPageForm(Page $page, $url, $urlParams = array(), $method = 'POST', $label = 'Submit')
	{
	    $form = $this->createForm(new PageType(), $page, array(
		'action' => $this->generateUrl($url, $urlParams),
		'method' => $method
	    ));

	    $form->add('submit', 'submit', array('label' => $label));

	    return $form;
	}
}
