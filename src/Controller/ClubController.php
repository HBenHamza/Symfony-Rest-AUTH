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
use App\Entity\Kindergarten;


class ClubController extends Controller{

	/**
     * @Route("/club/getAll", name="clubs")
     */
	public function getAllAction(Request $request){

		$helpers = $this->get(Helpers::class);

		$em = $this->getDoctrine()->getManager();

		$clubs = $em->getRepository(Club::class)->findAll();

		$data = array(
			"status" 	=> "success",
			"code" 		=> 200,
			"clubs" 		=> $clubs
		);

	return $helpers->json($data);
}

 /**
 * @Route("/club/getAllBykindergarten", name="getAllBykindergarten")
 */
public function getAllBykindergartenAction(Request $request){
	
	$helpers = $this->get(Helpers::class);
	$jwt_auth = $this->get(JwtAuth::class);

	$em = $this->getDoctrine()->getManager();
	
	$token = $request->get("authorization",null);
	$authCheck = $jwt_auth->checkToken($token);
	
	if($authCheck){
		$identity = $jwt_auth->checkToken($token, true);
		$id_kindergarten = $request->query->get('id_kindergarten');
		
		$clubs = $em->getRepository(Club::class)->findBy(array(
			'kindergarten' => $id_kindergarten
		));

		$data = array(
			"status" 	=> "success",
			"code" 		=> 200,
			"clubs" 		=> $clubs
		);
		
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
				$json = $parametersAsArray;
        	}	

			if($json != null){
				$createdAt = new \Datetime('now');
				$updatedAt = new \Datetime('now');

				$kinder_garten_id = (isset($json['kinder_garten_id'])) ? $json['kinder_garten_id'] : null;
				$title		= (isset($json['title'])) ? $json['title'] : null;
				$description= (isset($json['description'])) ? $json['description'] : null;


				if($title !=null && $description != null){

					$em = $this->getDoctrine()->getManager();

					$kinder_garten =  $em->getRepository(Kindergarten::class)->find($kinder_garten_id);

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
				$club = $em->getRepository(Club::class)->find($id);

				$em->remove($club);
				$em->flush();
				
				$data = array(
						"status" 	=> "success",
						"code" 		=> 200,
						"msg" 		=> "Club successfuly deleted"
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