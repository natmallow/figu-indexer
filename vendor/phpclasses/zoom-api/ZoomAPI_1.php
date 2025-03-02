<?php
namespace Zoom;

use Zoom\Endpoint\Users;

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
	 * Retorna uma instância única de uma classe.
	 *
	 * @staticvar Singleton $instance A instância única dessa classe.
	 *
	 * @return Singleton A Instância única.
	 */
	public function getInstance()
	{
		static $users = null;
		if (null === $users) {
			$this->users = new Users($this->apiKey, $this->apiSecret);
		}

		return $users;
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
