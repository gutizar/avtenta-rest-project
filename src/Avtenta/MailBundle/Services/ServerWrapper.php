<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Avtenta\MailBundle\Services;

use Fetch\Server;

/**
 * Description of ServerWrapper
 *
 * @author danielg
 */
class ServerWrapper 
{
    private $hostname;
    private $port;
    private $service;
    private $username;
    private $password;
    
    public function __construct($hostname, $port, $service, $username, $password)
    {
	$this->hostname = $hostname;
	$this->port = $port;
	$this->service = $service;
	$this->username = $username;
	$this->password = $password;
    }
    
    public function getConnection()
    {
	$server = new Server($this->hostname, $this->port, $this->service);
	$server->setAuthentication($this->username, $this->password);
	
	return $server;
    }
}
