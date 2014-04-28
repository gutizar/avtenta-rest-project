<?php

namespace Avtenta\MailBundle\Manager;

use Fetch\Message;
use Doctrine\Common\Persistence\ObjectManager;

class MailManager
{
    private $class;
    private $repository;
    private $objectManager;

    public function __construct(ObjectManager $objectManager, $class)
    {
	$this->class = $class;
	$this->objectManager = $objectManager;
	$this->repository = $objectManager->getRepository($class);
    }

    public function newEntity()
    {
	return new $this->class;
    }

    public function find($id)
    {
	return $this->repository->find($id);
    }

    public function exists($uid)
    {
	if (!$this->repository->findBy(array('uid' => $uid))) {
	    return false;
	}

	return true;
    }

    public function create(Message $message, $doSave = true)
    {
	$entity = $this->newEntity();

	$entity->setUid($message->getUid());
	$entity->setSender($message->getAddresses('from', true));
	$received = new \DateTime();
	$received->setTimestamp($message->getDate());
	$entity->setReceived($received);

	if ($doSave) {
	    $this->save($entity);
	}

	return $entity;
    }

    public function save($entity, $doFlush = true)
    {
	$this->objectManager->persist($entity);

	if ($doFlush) {
	    $this->objectManager->flush();
	}
    }
}