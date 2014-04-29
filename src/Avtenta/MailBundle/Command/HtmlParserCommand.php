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
use Symfony\Component\DomCrawler\Crawler;
use \HTMLPurifier as Purifier;

/**
 * Description of HtmlParserCommand
 *
 * @author danielg
 */
class HtmlParserCommand extends Command
{   
    private $purifier;
    
    public function __construct(Purifier $purifier)
    {
	parent::__construct();
	
	$this->purifier = $purifier;
    }
    
    protected function configure()
    {
	$this
		->setName('avtenta:mail:parse')
		->setDescription('Parses an HTML file')
		->addOption('path', '-p', InputOption::VALUE_OPTIONAL, 'Path of the HTML file to parse', 'c:\sample.html');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
	$path = $input->getOption('path');
	$html = file_get_contents($path);
	
	$html = $this->purifier->purify($html);
	file_put_contents('e:\Web\samples\cleaned.html', $html);
	
	$crawler = new Crawler($html);
	// Find the main content table
	$crawler = $crawler->filter('body > div > table');
	// Get the children and keep the table elements
	$crawler = $crawler->children()->filter('table');
	$output->writeln(sprintf('Found %s tables in the email', count($crawler)));
	// Keep the first table element only - customer data
	$customerData = $this->parseCustomerDataTable($crawler->first());
	// $orderData = $this->parseOrderDataTable($crawler->filter('table:nth-child(2)'));
	// $summaryData = $this->parseSummaryDataTable($crawler->filter('table:nth-child(3)'));
	// $priceData = $this->parsePriceDataTable($crawler->last());
	
	var_dump($customerData);
    }
    
    private function parseCustomerDataTable(Crawler $crawler)
    {
	$customerData = new \stdClass();
	
	$rows = $crawler->filter('tr');
	
	$customerData->sender = $this->parseUserData(
		$rows->filter('tr:nth-child(2)')->filter('td')->first()
	);
	$customerData->recipient = $this->parseUserData(
		$rows->filter('tr:nth-child(2)')->filter('td')->last()
	);
	$customerData->dispatch = $this->parseDispatchType($rows->filter('tr:nth-child(5)'));
	$customerData->payment = $this->parsePaymentType($rows->filter('tr:nth-child(8)'));
	
	return $customerData;
    }
    
    private function parseUserData(Crawler $crawler)
    {
	$userData = new \stdClass();
	
	$userData->name = $crawler->filter('span')->first()->text();
	
	return $userData;
    }
    
    private function parseDispatchType(Crawler $crawler)
    {
	return $crawler->filter('span')->first()->text();
    }
    
    private function parsePaymentType(Crawler $crawler)
    {
	return $crawler->filter('span')->first()->text();
    }
}
