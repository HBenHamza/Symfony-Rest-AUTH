<?php

namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\Owner;

class JwtAuth{

	public $manager;
	public $key;

	public function __construct($manager){
		$this->manager = $manager;
		$this->key = 'thisIsANiceKey!@#!3!!21$%ˆ&*()';

	}

	public function signup($email, $password, $getHash = null){

		$user = $this->manager->getRepository(Owner::class)->findOneBy(array(
			"email" => $email,
			"password" => $password
		));

		$signup = false;
		if(is_object($user)){
			$signup = true;
		}

		if($signup){
			//generate token
			$token = array(
				"id" => $user->getId(),
				"email" => $user->getEmail(),
				"first_name" => $user->getFirstName(),
				"last_name" => $user->getLastName(),
				"iat" => time(),
				"exp" => time() + (7*24*60*60)
			);

			$jwt 		= JWT::encode($token, $this->key, 'HS256');
			$decoded 	= JWT::decode($jwt, $this->key, array('HS256'));

			if($getHash == null){
				$data = $jwt;
			}else{
				$data = $decoded;
			}

		}else{
			$data = array(
				'status' => 'error',
				'data'	 => 'Login failed!!'
			);
		}

		return $data;
	}

	public function checkToken($jwt, $getIdentity = false){

		$auth = false;

		try{
			$decoded = JWT::decode($jwt, $this->key, array('HS256'));
		}catch(\UnexpectedValueException $e){
			$auth = false;
		}catch(\DomainException $e){
			$auth = false;
		}

		if(isset($decoded) && is_object($decoded) && isset($decoded->id)){
			$auth = true;
		}else{
			$auth = false;
		}

		if($getIdentity == false){
			return $auth;
		}else{
			return $decoded;
		}
	}
}