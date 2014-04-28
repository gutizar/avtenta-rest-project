<?php

namespace Avtenta\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
	$server = $this->get('avtenta.mail')->getConnection();
	$manager = $this->get('avtenta.manager.mail');

	$messages = $server->getMessages(1);
	$processedMessages = array();

	foreach ($messages as $message) 
	{
	    if (!$manager->exists($message->getUid())) {
		$processedMessages[] = $manager->create($message);
	    }
	}

	return array(
	    'mails' => $processedMessages,
	    'name' => $name
	);
    }
}
