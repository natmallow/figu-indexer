<?php
namespace Zoom;

use Zoom\Endpoint\Users;
use Zoom\Endpoint\Meetings;

class ZoomAPI{

	/**
	 * @var null
	 */
	private $apiKey = null;

	/**
	 * @var null
	 */
	private $apiSecret = null;

	/**
	 * @var null
	 */
	public $users = null;
	
        /**
	 * @var null
	 */
	public $meetings = null;

        

	/**
	 * Retorna uma instância única de uma classe.
	 *
	 * @staticvar Singleton $instance A instância única dessa classe.
	 *
	 * @return Singleton A Instância única.
	 */
	public function getInstance()
	{
		static $meetings = null;

		if (null === $meetings) {
			$this->meetings = new Meetings($this->apiKey, $this->apiSecret);
		}

		return $meetings;
	}

	/**
	 * Zoom constructor.
	 * @param $apiKey
	 * @param $apiSecret
	 */
	public function __construct( $apiKey, $apiSecret ) {

		$this->apiKey = $apiKey;

		$this->apiSecret = $apiSecret;

		$this->getInstance();

	}


	/*Functions for management of users*/

	public function createUser(){
		$createAUserArray['action'] = 'create';
		$createAUserArray['email'] = $_POST['email'];
		$createAUserArray['user_info'] = $_POST['user_info'];

		return $this->users->create($createAUserArray);
	}
}

?> 
