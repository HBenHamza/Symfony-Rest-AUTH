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


class KindergartenController extends Controller{

	/**
     * @Route("/kindergarten/getAll", name="kindergartens")
     */
	public function getAllAction(Request $request){

			$helpers = $this->get(Helpers::class);

			$em = $this->getDoctrine()->getManager();
	
			$kinder_gartens = $em->getRepository(Kindergarten::class)->findAll();

			$data = array(
				"status" 	=> "success",
				"code" 		=> 200,
				"kindergartens" 		=> $kinder_gartens
			);

		return $helpers->json($data);
	}

 	/**
     * @Route("/kindergarten/getAllByOwner", name="kindergartensByOwner")
     */
	public function getAllByOwnerAction(Request $request){
		
		$helpers = $this->get(Helpers::class);
		$jwt_auth = $this->get(JwtAuth::class);

		$em = $this->getDoctrine()->getManager();
		
		$token = $request->get("authorization",null);
		$authCheck = $jwt_auth->checkToken($token);
		
		if($authCheck){
			$identity = $jwt_auth->checkToken($token, true);
            $owner_id = $request->query->get('owner_id');
            
			$kinder_gartens = $em->getRepository(Kindergarten::class)->findBy(array(
				'owner' => $owner_id
			));

			$data = array(
				"status" 	=> "success",
				"code" 		=> 200,
				"kindergartens" 		=> $kinder_gartens
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
     * @Route("/kindergarten/new", name="new_kindergarten")
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

				$owner_id 	= ($identity->id !=null) ? $identity->id : null;
				$name		= (isset($json['name'])) ? $json['name'] : null;
				$doucmentEvidenceTva= (isset($json['documentEvidenceTva'])) ? $json['documentEvidenceTva'] : null;
				$ratingNote= (isset($json['ratingNote'])) ? $json['ratingNote'] : null;
				$description= (isset($json['description'])) ? $json['description'] : null;
				$avalability= (isset($json['avalability'])) ? $json['avalability'] : null;
				$address= (isset($json['address'])) ? $json['address'] : null;
				$tags= (isset($json['tags'])) ? $json['tags'] : null;
				$capacity= (isset($json['capacity'])) ? $json['capacity'] : null;
				$nb_children_registered= (isset($json['nb_children_registered'])) ? $json['nb_children_registered'] : null;
				$picture= (isset($json['ratingNote'])) ? $json['ratingNote'] : null;
				


				if($name !=null && $doucmentEvidenceTva !=null &&
				$ratingNote !=null && $description !=null && $avalability !=null && $address !=null && 
				$tags !=null && $capacity !=null && $nb_children_registered !="" && $picture !=null){
					
					$em = $this->getDoctrine()->getManager();
					$owner = $em->getRepository(Owner::class)->findOneBy(array(
						'id' => $owner_id
					));

					$kindergarten = new Kindergarten();
					$kindergarten->SetName($name);
					$kindergarten->SetDoucmentEvidenceTva($doucmentEvidenceTva);
					$kindergarten->SetRatingNote((int)$ratingNote);
					$kindergarten->SetDescription($description);
					$kindergarten->SetAvalability((int)$avalability);
					$kindergarten->SetAddress($address);
					$kindergarten->SetTags($tags);
					$kindergarten->SetCapacity($capacity);
					$kindergarten->SetNbChildrenRegistered((int)$nb_children_registered);
					$kindergarten->SetPicture($picture);
					$kindergarten->setCreatedDate($createdAt);
					$kindergarten->setOwner($owner);
					

					$em->persist($kindergarten);
					$em->flush();

					$data = array(
							"status" 	=> "success",
							"code" 		=> 200,
							"msg" 		=> "Kindergarten suucessfuly created "
					);
				}else{
					$data = array(
						"status" 	=> "error",
						"code" 		=> 400,
						"msg" 		=> "Kindergarten not created, validation failed"
					);
				}

			}else{
				$data = array(
					"status" 	=> "error",
					"code" 		=> 400,
					"msg" 		=> "Kindergarten not created, params failed"
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
     * @Route("/kindergarten/edit", name="edit_kindergarten")
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
				$json = $parametersAsArray;
        	}	

			if($json != null && isset($json['id_kindergarten'])){
				
				$id = $json['id_kindergarten'];

				$em = $this->getDoctrine()->getManager();

                $kindergarten = $em->getRepository(Kindergarten::class)->find($id);


				if($kindergarten){
						$createdAt = new \Datetime('now');

					$owner_id 	= ($identity->id !=null) ? $identity->id : null;
					$name		= (isset($json['name'])) ? $json['name'] : $kindergarten->getName();
					$doucmentEvidenceTva= (isset($json['documentEvidenceTva'])) ? $json['documentEvidenceTva'] : $kindergarten->getDoucmentEvidenceTva();
					$ratingNote= (isset($json['ratingNote'])) ? $json['ratingNote'] : $kindergarten->getRatingNote();
					$description= (isset($json['description'])) ? $json['description'] : $kindergarten->getDescription();
					$avalability= (isset($json['avalability'])) ? $json['avalability'] : $kindergarten->getAvalability();
					$address= (isset($json['address'])) ? $json['address'] : $kindergarten->getAddress();
					$tags= (isset($json['tags'])) ? $json['tags'] : $kindergarten->getTags();
					$capacity= (isset($json['capacity'])) ? $json['capacity'] : $kindergarten->getCapacity();
					$nb_children_registered= (isset($json['nb_children_registered'])) ? $json['nb_children_registered'] : $kindergarten->getNBChildrenRegistered();
					$picture= (isset($json['picture'])) ? $json['picture'] : $kindergarten->getPicture();

					if($name !=null && $doucmentEvidenceTva !=null &&
					$ratingNote !=null && $description !=null && $avalability !=null && $address !=null && 
					$tags !=null && $capacity !=null && $nb_children_registered !="" && $picture !=null){
						
						$em = $this->getDoctrine()->getManager();
						$owner = $em->getRepository(Owner::class)->findOneBy(array(
							'id' => $owner_id
						));

						$kindergarten->SetName($name);
						$kindergarten->SetDoucmentEvidenceTva($doucmentEvidenceTva);
						$kindergarten->SetRatingNote((int)$ratingNote);
						$kindergarten->SetDescription($description);
						$kindergarten->SetAvalability((int)$avalability);
						$kindergarten->SetAddress($address);
						$kindergarten->SetTags($tags);
						$kindergarten->SetCapacity($capacity);
						$kindergarten->SetNbChildrenRegistered((int)$nb_children_registered);
						$kindergarten->SetPicture($picture);
						$kindergarten->setCreatedDate($createdAt);
						$kindergarten->setOwner($owner);
						

						$em->persist($kindergarten);
						$em->flush();

						$data = array(
								"status" 	=> "success",
								"code" 		=> 200,
								"msg" 		=> "Kindergarten suucessfuly updated "
						);
					}else{
						$data = array(
							"status" 	=> "error",
							"code" 		=> 400,
							"msg" 		=> "Kindergarten not updated, validation failed"
						);
					}
				}else{
					$data = array(
						"status" 	=> "error",
						"code" 		=> 400,
						"msg" 		=> "Kindergarten not found"
					);
				}
				

			}else{
				$data = array(
					"status" 	=> "error",
					"code" 		=> 400,
					"msg" 		=> "Kindergarten not updated, params failed"
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
     * @Route("/kindergarten/delete", name="delete_kindergarten")
     */
	public function deleteAction(Request $request, $id=null){
		$helpers = $this->get(Helpers::class);
		$jwt_auth = $this->get(JwtAuth::class);

		$token = $request->get("authorization",null);
		$authCheck = $jwt_auth->checkToken($token);
		
		if($authCheck){
			$identity = $jwt_auth->checkToken($token, true);
		
			$id = $request->query->get("id_kindergarten");
			if($id != null){
				
				$em = $this->getDoctrine()->getManager();
				$kindergarten = $em->getRepository(Kindergarten::class)->find($id);

				$em->remove($kindergarten);
				$em->flush();
				
				$data = array(
						"status" 	=> "success",
						"code" 		=> 200,
						"msg" 		=> "kindergarten successfuly deleted"
					);


			}else{
					$data = array(
						"status" 	=> "error",
						"code" 		=> 400,
						"msg" 		=> "kindergarten not deleted, params failed"
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