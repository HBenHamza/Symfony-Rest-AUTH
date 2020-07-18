<?php

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use App\Services\Helpers;
use App\Services\JwtAuth;

use App\Entity\Owner;

class UserController extends Controller
{
    /**
     * @Route("/login", name="user_sign_in")
     */
    public function loginAction(Request $request){        
        
        $helpers = $this->get(Helpers::class);
        
        if ($json = $request->getContent()) {
            $parametersAsArray = json_decode($json, true);

            
            $email = ($parametersAsArray['email']) ? $parametersAsArray['email'] : null ;
            $password = ($parametersAsArray['password']) ? $parametersAsArray['password'] : null ;
            $getHash = null ;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This email is not valid!";
            $validate_email = $this->get('validator')->validate($email, $emailConstraint);

            $pwd = hash('sha256', $password);

            if(count($validate_email) == 0 && $password != null){

                $jwt_auth = $this->get(JwtAuth::class);

                if($getHash == null || $getHash == false){
                    $signup = $jwt_auth->signup($email, $pwd);
                }else{
                    $signup = $jwt_auth->signup($email, $pwd, true);
                }

                return $this->json($signup);

            }else{
                $data = array(
                    'status'=>'error',
                    'data'=>'Email or password Incorrect.'
                );
            }
        }else{
            $data = array(
                'status'=>'error',
                'data'=>'Send json via post.'
            );
    
        }

        return $helpers->json($data);
    }

    /**
     * @Route("/owner/new", name="new_owner")
     */
    public function newAction(Request $request){
        $helpers = $this->get(Helpers::class);

        if ($json = $request->getContent()) {
            $parametersAsArray = json_decode($json, true);
            
            $createdAt = new \Datetime("now");
            $role = 'user';

            $email = ($parametersAsArray['email']) ? $parametersAsArray['email'] : null ;
            $password = ($parametersAsArray['password']) ? $parametersAsArray['password'] : null ;
            $first_name = ($parametersAsArray['first_name']) ? $parametersAsArray['first_name'] : null ;
            $last_name = ($parametersAsArray['last_name']) ? $parametersAsArray['last_name'] : null ;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This email is not valid!";
            $validate_email = $this->get("validator")->validate($email, $emailConstraint);

            if($email != null && count($validate_email)==0 && $password != null && $first_name != null && $last_name != null){

                $owner = new Owner();
                $owner->setCreatedAt($createdAt);
                $owner->setEmail($email);
                $owner->setFirstName($first_name);
                $owner->setLastName($last_name);

                $pwd = hash('sha256',$password);
                $owner->setPassword($pwd);

                $em = $this->getDoctrine()->getManager();
                $isset_user = $em->getRepository(Owner::class)->findBy(array(
                    "email" => $email
                ));

                if(count($isset_user)==0){
                    $em->persist($owner);
                    $em->flush();

                    $data = array(
                        'status' => 'success',
                        'code'   => 200,
                        'msg'    => 'New Owner created !!',
                        'user'   => $owner
                    );
                }else{
                    $data = array(
                        'status' => 'error',
                        'code'   => 400,
                        'msg'    => 'Owner not created, duplicated !!'
                    );
                }
            }

        }else{
            $data = array(
                'status' => 'error',
                'code'   => 400,
                'msg'    => 'Owner not created!' 
            );
        }

        return $helpers->json($data);
        
    }

    /**
     * @Route("/owner/edit", name="owner_user")
     */
    public function editAction(Request $request){
        $helpers = $this->get(Helpers::class);
        $jwt_auth = $this->get(JwtAuth::class);

        $token = $request->get("authorization",null);//this variable comes in $POST

        $authCheck = $jwt_auth->checkToken($token);

        if($authCheck){
                //entity manager
                $em = $this->getDoctrine()->getManager();

                //get the user info via token
                $identity = $jwt_auth->checkToken($token, true);

                //get the user object to update
                $owner = $em->getRepository(Owner::class)->findOneBy(array(
                    'id' => $identity->sub
                ));

                $json = $request->get("json", null);
                $params = json_decode($json);

                $data = array(
                    'status' => 'error',
                    'code'   => 400,
                    'msg'    => 'Owner not created!' 
                );

            if($json !=null){
                //$createdAt = new \Datetime("now");
                $role = 'user';

                $email = (isset($params->email)) ? $params->email : null;
                $name = (isset($params->name)) ? $params->name : null;
                $surname = (isset($params->surname)) ? $params->surname : null;
                $password = (isset($params->password)) ? $params->password : null;

                $emailConstraint = new Assert\Email();
                $emailConstraint->message = "This email is not valid!";
                $validate_email = $this->get("validator")->validate($email, $emailConstraint);

                if($email != null && count($validate_email)==0 || $password != null || $name != null || $surname != null){

                    //$owner->setCreatedAt($createdAt);
                    $owner->setRole($role);
                    $owner->setEmail($email);
                    $owner->setName($name);
                    $owner->setSurname($surname);

                    if($password!=null){
                        $pwd=hash('sha256', $password);
                        $owner->setPassword($pwd);
                    } 

                    $isset_user = $em->getRepository(Owner::class)->findBy(array(
                        "email" => $email
                    ));

                    if(count($isset_user)==0 || $identity->email == $email){
                        $em->persist($owner);
                        $em->flush();

                        $data = array(
                            'status' => 'success',
                            'code'   => 200,
                            'msg'    => 'New owner updated !!',
                            'user'   => $owner
                        );
                    }else{
                        $data = array(
                            'status' => 'error',
                            'code'   => 400,
                            'msg'    => 'owner not updated, duplicated !!'
                        );
                    }
                }
            }
        }else{
                $data = array(
                    'status' => 'error',
                    'code'   => 400,
                    'msg'    => 'Authorization not valid!'  
                );
        }
        return $helpers->json($data);
        
    }
}