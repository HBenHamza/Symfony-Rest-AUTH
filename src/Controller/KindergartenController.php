<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use App\Services\Helpers;
use App\Services\JwtAuth;
use App\Services\FileUploader;
use Symfony\Component\String\Slugger\SluggerInterface;


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
		


				$createdAt = new \Datetime('now');
				
				$owner_id 	= ($identity->id !=null) ? $identity->id : null;
				$name		= !empty($request->request->get('name')) ? $request->request->get('name') : null;
				$ratingNote= !empty($request->request->get('ratingNote')) ? $request->request->get('ratingNote') : null;
				$description= !empty($request->request->get('description')) ?$request->request->get('description') : null;
				$avalability= !empty($request->request->get('avalability')) ?$request->request->get('avalability') : null;
				$address= !empty($request->request->get('address')) ? $request->request->get('address') : null;
				$tags= !empty($request->request->get('tags')) ? $request->request->get('tags') : null;
				$capacity= !empty($request->request->get('capacity')) ?$request->request->get('capacity') : null;
				$nb_children_registered= !empty($request->request->get('nb_children_registered')) ?$request->request->get('nb_children_registered') : null;
				
				$kindergarten = new Kindergarten();

				$doucmentEvidenceTva = $request->files->get('doucmentEvidenceTva');
				$picture = $request->files->get('picture');


				// this condition is needed because the 'brochure' field is not required
				// so the PDF file must be processed only when a file is uploaded
				if ($doucmentEvidenceTva) {
					$originalFilename = pathinfo($doucmentEvidenceTva->getClientOriginalName(), PATHINFO_FILENAME);
						// this is needed to safely include the file name as part of the URL
						$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
						$newFilename = $safeFilename.'-'.uniqid().'.'.$doucmentEvidenceTva->guessExtension();


					// Move the file to the directory where brochures are stored
					try {
						$doucmentEvidenceTva->move(
							$this->getParameter('upload_dir'),
							$newFilename
						);
					} catch (FileException $e) {
						// ... handle exception if something happens during file upload
					}
					$kindergarten->SetDoucmentEvidenceTva($newFilename);
					}else{
						$kindergarten->SetDoucmentEvidenceTva("...");
					}

					if ($picture) {
						$originalFilename1 = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
							// this is needed to safely include the file name as part of the URL
							$safeFilename1 = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename1);
							$newFilename1 = $safeFilename1.'-'.uniqid().'.'.$picture->guessExtension();
	
	
						// Move the file to the directory where brochures are stored
						try {
							$picture->move(
								$this->getParameter('upload_dir'),
								$newFilename1
							);
						} catch (FileException $e) {
							// ... handle exception if something happens during file upload
						}
							$kindergarten->SetPicture($newFilename1);
						}else{
							$kindergarten->SetPicture("...");
						}
					
						
					if($name && $ratingNote && $description && $avalability && $address && 
					$tags && $capacity  && $nb_children_registered){
						
						$em = $this->getDoctrine()->getManager();
						$owner = $em->getRepository(Owner::class)->find($owner_id);
					
					$kindergarten->SetName($name);
					$kindergarten->SetRatingNote((int)$ratingNote);
					$kindergarten->SetDescription($description);
					$kindergarten->SetAvalability((int)$avalability);
					$kindergarten->SetAddress($address);
					$kindergarten->SetTags($tags);
					$kindergarten->SetCapacity($capacity);
					$kindergarten->SetNbChildrenRegistered((int)$nb_children_registered);
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
						"status" 	=> "success",
						"code" 		=> 200,
						"msg" 		=> "Kindergarten not created validation error "
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
		


				$createdAt = new \Datetime('now');

				$owner_id 	= ($identity->id !=null) ? $identity->id : null;
				$name		= !empty($request->request->get('name')) ? $request->request->get('name') : $kindergarten->getName();
				$ratingNote= !empty($request->request->get('ratingNote')) ? $request->request->get('ratingNote') : $kindergarten->getRatingNote();
				$description= !empty($request->request->get('description')) ?$request->request->get('description') :  $kindergarten->getDescription();
				$avalability= !empty($request->request->get('avalability')) ?$request->request->get('avalability') : $kindergarten->getAvalability();
				$address= !empty($request->request->get('address')) ? $request->request->get('address') : $kindergarten->getAddress();
				$tags= !empty($request->request->get('tags')) ? $request->request->get('tags') : $kindergarten->getTags();
				$capacity= !empty($request->request->get('capacity')) ?$request->request->get('capacity') : $kindergarten->getCapacity();
				$nb_children_registered= !empty($request->request->get('nb_children_registered')) ?$request->request->get('nb_children_registered') : $kindergarten->getNBChildrenRegistered();
				
                $em = $this->getDoctrine()->getManager();

                $kindergarten = $em->getRepository(Kindergarten::class)->find($request->request->get('id_kindergarten'));

				$doucmentEvidenceTva = $request->files->get('doucmentEvidenceTva');
				$picture = $request->files->get('picture');


				// this condition is needed because the 'brochure' field is not required
				// so the PDF file must be processed only when a file is uploaded
				if ($doucmentEvidenceTva) {
					$originalFilename = pathinfo($doucmentEvidenceTva->getClientOriginalName(), PATHINFO_FILENAME);
						// this is needed to safely include the file name as part of the URL
						$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
						$newFilename = $safeFilename.'-'.uniqid().'.'.$doucmentEvidenceTva->guessExtension();


					// Move the file to the directory where brochures are stored
					try {
						$doucmentEvidenceTva->move(
							$this->getParameter('upload_dir'),
							$newFilename
						);
					} catch (FileException $e) {
						// ... handle exception if something happens during file upload
					}
						$kindergarten->SetDoucmentEvidenceTva($newFilename);
					}else{
						$kindergarten->SetDoucmentEvidenceTva($kindergarten->getDoucmentEvidenceTva());
					}

					if ($picture) {
						$originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
							// this is needed to safely include the file name as part of the URL
							$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
							$newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();
	
	
						// Move the file to the directory where brochures are stored
						try {
							$picture->move(
								$this->getParameter('upload_dir'),
								$newFilename
							);
						} catch (FileException $e) {
							// ... handle exception if something happens during file upload
						}
							$kindergarten->SetPicture($newFilename);
						}else{
							$kindergarten->SetPicture($kindergarten->getPicture());
						}
					
						
					if($name && $ratingNote && $description && $avalability && $address && 
					$tags && $capacity  && $nb_children_registered){
						
						$em = $this->getDoctrine()->getManager();
						$owner = $em->getRepository(Owner::class)->findOneBy(array(
							'id' => $owner_id
						));

					$kindergarten->SetName($name);
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
							"msg" 		=> "Kindergarten suucessfuly edited "
					);
				}else{
					$data = array(
						"status" 	=> "success",
						"code" 		=> 200,
						"msg" 		=> "Kindergarten not edited validation error "
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