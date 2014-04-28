<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Avtenta\MailBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Avtenta\MailBundle\Manager\MailManager;
use Avtenta\MailBundle\Services\ServerWrapper;

/**
 * Description of MailImporterCommand
 *
 * @author danielg
 */
class MailImporterCommand extends Command
{
    protected $manager;
    protected $mailer;
    
    public function __construct(MailManager $manager, ServerWrapper $mailer)
    {
	parent::__construct();
	
	$this->manager = $manager;
	$this->mailer = $mailer;
    }
    
    protected function configure()
    {
	$this
		->setName('avtenta:mail:load')
		->setDescription('Loads new mails into the system')
		->addOption('count', '-c', InputOption::VALUE_OPTIONAL, 'Number of new mails to fetch each time', 10);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
	$connection = $this->mailer->getConnection();
	$count = $input->getOption('count');
	
	$messages = $connection->getRecentMessages($count);
	foreach($messages as $message)
	{
	    if (!$this->manager->exists($message->getUid()))
	    {
		$output->writeln(sprintf('Found new mail with UID %s', $message->getUid()));
		$this->manager->create($message);
	    }
	}
    }
}
