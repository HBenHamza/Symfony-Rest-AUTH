<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use App\Services\Helpers;
use App\Services\JwtAuth;

use App\Entity\Club;
use App\Entity\Owner;
use App\Entity\KinderGarten;


class ClubController extends Controller{

 	/**
     * @Route("/club/new", name="new_club")
     */
	public function newAction(Request $request, $id=null){
		$helpers = $this->get(Helpers::class);
		$jwt_auth = $this->get(JwtAuth::class);

		$token = $request->get("authorization",null);
		$authCheck = $jwt_auth->checkToken($token);
		
		if($authCheck){
			$identity = $jwt_auth->checkToken($token, true);
			if ($json = $request->getContent()) {
            	$parametersAsArray = json_decode($json, true);
        	}	
			$json = $parametersAsArray;

			if($json != null){

				$createdAt = new \Datetime('now');
				$updatedAt = new \Datetime('now');

				$user_id 	= ($identity->id !=null) ? $identity->id : null;
				$kinder_garten_id = (isset($params->kinder_garten_id)) ? $params->kinder_garten_id : null;
				$title		= (isset($json['title'])) ? $json['title'] : null;
				$description= (isset($json['description'])) ? $json['description'] : null;


				if($user_id != null && $title !=null){

					$em = $this->getDoctrine()->getManager();
					$user = $em->getRepository(Owner::class)->findOneBy(array(
						'id' => $user_id
					));

					$kinder_garten =  $em->getRepository(KinderGarten::class)->findOneBy(array(
						'id' => $kinder_garten_id
					));

					if($id==null){
						$club = new Club();
						$club->setKindergarten($kinder_garten);
						$club->setTitle($title);
						$club->setDescription($description);
						$club->setCreatedDate($createdAt);
						$club->setUpdatedDate($updatedAt);

						$em->persist($club);
						$em->flush();

						$data = array(
							"status" 	=> "success",
							"code" 		=> 200,
							"data" 		=> $club
						);
					}

					

				}else{
					$data = array(
						"status" 	=> "error",
						"code" 		=> 400,
						"msg" 		=> "Club not created, validation failed"
					);
				}

			}else{
				$data = array(
					"status" 	=> "error",
					"code" 		=> 400,
					"msg" 		=> "Club not created, params failed"
				);
			}

			
		}else{
			$data = array(
				"status" 	=> "error",
				"code" 		=> 400,
				"msg" 		=> "Authorization not valid !!"
			);
		}

		return $helpers->json($data);
	}

	/**
     * @Route("/club/edit", name="edit_club")
     */
	public function editAction(Request $request, $id=null){
		$helpers = $this->get(Helpers::class);
		$jwt_auth = $this->get(JwtAuth::class);

		$token = $request->get("authorization",null);
		$authCheck = $jwt_auth->checkToken($token);
		
		if($authCheck){
			$identity = $jwt_auth->checkToken($token, true);
			if ($json = $request->getContent()) {
            	$parametersAsArray = json_decode($json, true);
        	}	
			$json = $parametersAsArray;

			if($json != null){
				$id 	= (isset($json['id_club'])) ? (isset($json['id_club'])) : null;

				$em = $this->getDoctrine()->getManager();

                $clubs = $em->getRepository(Club::class)->findAll();


				$kinder_garten_id = (isset($json['kinder_garten_id'])) ? $json['kinder_garten_id'] : $club->getKinderGartenId();
				$title	= (isset($json['title'])) ? $json['title'] : $club->getTitle();
				$description= (isset($json['description'])) ? $json['description'] : $club->getDescription();

				$updatesClub = $clubs[array_search($id, $clubs)];
				$kinder_garten =  $em->getRepository(KinderGarten::class)->findOneBy(array(
						'id' => $kinder_garten_id
					));

				$updatedAt = new \Datetime('now');
				$updatesClub->setKinderGarten($kinder_garten);
				$updatesClub->setTitle($title);
				$updatesClub->setDescription($description);
				$updatesClub->setUpdatedDate($updatedAt);

				$em->persist($updatesClub);
				$em->flush();

				$data = array(
						"status" 	=> "Success",
						"code" 		=> 400,
						"msg" 		=> "Club updated"
					);
			}else{
				$data = array(
					"status" 	=> "error",
					"code" 		=> 400,
					"msg" 		=> "Club not created, params failed"
				);
			}
		}else{
					$data = array(
						"status" 	=> "error",
						"code" 		=> 400,
						"msg" 		=> "Club not created, validation failed"
					);
				}

		return $helpers->json($data);
	
	}

	/**
     * @Route("/club/delete", name="delete_club")
     */
	public function deleteAction(Request $request, $id=null){
		$helpers = $this->get(Helpers::class);
		$jwt_auth = $this->get(JwtAuth::class);

		$token = $request->get("authorization",null);
		$authCheck = $jwt_auth->checkToken($token);
		
		if($authCheck){
			$identity = $jwt_auth->checkToken($token, true);
		
			$id = $request->query->get("id_club");
			if($id != null){

				$em = $this->getDoctrine()->getManager();
				$club = $em->getRepository(Club::class)->findOneBy(array(
					'id' => $id
				));

				$em->remove($club);
				$em->flush();
				
				$data = array(
						"status" 	=> "success",
						"code" 		=> 200,
						"msg" 		=> "Club deleted"
					);


			}else{
					$data = array(
						"status" 	=> "error",
						"code" 		=> 400,
						"msg" 		=> "Club not deleted, params failed"
					);
			}	
		}else{
			$data = array(
				"status" 	=> "error",
				"code" 		=> 400,
				"msg" 		=> "Authorization not valid !!"
			);
		}

		return $helpers->json($data);
	}
}