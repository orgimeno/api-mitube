<?php

namespace AppBundle\Controller;

use BackendBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Asserts;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @throws \Exception
     */
    public function newAction(Request $request){

        $helpers = $this->get('app.helpers');

        $json = $request->get("json", null);

        $params = json_decode($json);

        $data = [
            'status' => 'error',
            'code' => 400,
            'msg' => "User Failed"
        ];

        if($params != null){

            $createAt = new \DateTime("now");
            $image = null;
            $role = "user";
            $email = (isset($params->email)) ? $params->email : null;
            $name = (isset($params->name) && ctype_alpha($params->name)) ? $params->name : null;
            $surname = (isset($params->surname) && ctype_alpha($params->surname)) ? $params->surname : null;
            $password = (isset($params->password)) ? $params->password : null;


            $emConstrait = new Asserts\Email();
            $emConstrait->message = "This email is not valid";
            $validate_mail = $this->get('validator')->validate($email, $emConstrait);

            if(count($validate_mail) == 0 && $password != null && $name != null && $surname != null){

                $user = new User();
                $user->setCreatedAt($createAt);
                $user->setImage($image);
                $user->setRole($role);
                $user->setEmail($email);
                $user->setName($name);
                $user->setSurname($surname);

                //Cifrar pass

                $pwd = hash('sha256', $password);

                $user->setPassword($pwd);

                $em = $this->getDoctrine()->getManager();
                $isset_user = $em->getRepository(User::class)->findOneBy([
                    'email' => $email
                ]);

                /** @var User $isset_user */
                if(is_null($isset_user)){
                    $em->persist($user);
                    $em->flush();

                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'msg' => "User created"
                    ];
                }else{
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'msg' => "User already created"
                    ];
                }

                return new JsonResponse($data);

            }else{
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'msg' => "User request incorrect or unfinished"
                ];
                return new JsonResponse($data);
            }

        }else{
            $data = [
                'status' => 'error',
                'code' => 400,
                'msg' => "User not created"
            ];
        }

        return $helpers->json($data);
    }

    /**
     * @param Request $request
     */
    public function editAction(Request $request){

        $helpers = $this->get('app.helpers');

        $hash = $request->get('authorization', null);
        $authCheck = $helpers->authCheck($hash);

        if($authCheck){

            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->find($identity->sub);

            $json = $request->get("json", null);

            $params = json_decode($json);

            $data = [
                'status' => 'error',
                'code' => 400,
                'msg' => "User Failed"
            ];

            if($params != null){

                $createAt = new \DateTime("now");
                $image = null;
                $role = "user";
                $email = (isset($params->email)) ? $params->email : null;
                $name = isset($params->name) ? $params->name : null;
                $surname = isset($params->surname)  ? $params->surname : null;
                $password = (isset($params->password) && !empty($params->password)) ? $params->password : null;


                $emConstrait = new Asserts\Email();
                $emConstrait->message = "This email is not valid";
                $validate_mail = $this->get('validator')->validate($email, $emConstrait);

                if(count($validate_mail) == 0
                    && $name != null
                    && $surname != null){

                    $user->setCreateAt($createAt);
                    $user->setRole($role);
                    $user->setEmail($email);
                    $user->setName($name);
                    $user->setSurname($surname);

                    if($password != null){

                        //Cifrar pass

                        $pwd = hash('sha256', $password);

                        $user->setPassword($pwd);
                    }

                    $isset_user = $em->getRepository(User::class)->findOneBy([
                        'email' => $email
                    ]);

                    /** @var User $isset_user */
                    if(is_null($isset_user) || $email == $identity->email){
                        $em->persist($user);
                        $em->flush();

                        $data = [
                            'status' => 'success',
                            'code' => 200,
                            'msg' => "User updated"
                        ];
                    }else{
                        $data = [
                            'status' => 'error',
                            'code' => 400,
                            'msg' => "User already updated"
                        ];
                    }

                    return new JsonResponse($data);

                }else{
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'msg' => "User request incorrect or unfinished"
                    ];
                    return new JsonResponse($data);
                }

            }else{
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'msg' => "User not updated"
                ];
            }

            return $helpers->json($data);
        }else{
            $data = [
              'status' => "error",
              'code' => 400,
              'msg' => "Authorization not valid"
            ];

            return $helpers->json($data);
        }
    }

    /**
     * @param Request $request
     */
    public function uploadImageAction(Request $request){
        $helpers = $this->get('app.helpers');
        $hash = $request->get('authorization', null);
        $authCheck = $helpers->authCheck($hash);

        if($authCheck){
            $identity = $helpers->authCheck($hash, true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($identity->sub);

            // Upload image
            $file = $request->files->get('image');

            if(!empty($file) && $file != null){
                $ext = $file->guessExtension();

                if($ext === 'jpg' || $ext === 'gif'
                || $ext === 'jpeg' || $ext === 'png'){
                    $file_name = time().".".$ext;
                    $file->move("uploads/users", $file_name);

                    $user->setImage($file_name);

                    $em->persist($user);
                    $em->flush();

                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'msg' => "Image uploaded"
                    ];

                }else{
                    $data = [
                        'status' => 'error',
                        'code' => 461,
                        'msg' => "La imagen tiene una extensión no válida"
                    ];
                }
            }else{

                $data = [
                    'status' => 'error',
                    'code' => 460,
                    'msg' => "Image not uploaded"
                ];
            }

        }else{
            $data = [
                'status' => "error",
                'code' => 400,
                'msg' => "Authorazation not valid"
            ];
        }

        return $helpers->json($data);
    }


    /**
     * @param Request $request
     */
    public function channelAction(Request $request, $id = null){

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();

        if(!is_null($id)){

            $user = $em->getRepository(User::class)->find($id);

            $dql = "SELECT v from BackendBundle:Video v ORDER BY v.id DESC";

            $query = $em->createQuery($dql);

            $page = $request->query->getInt("page", 1);

            $paginator = $this->get("knp_paginator");

            $items_per_page = 6;

            $pagination = $paginator->paginate($query, $page, $items_per_page);
            $total_items_count = $pagination->getTotalItemCount();

            $data = [
                "status" => "success",
                "total_items_count" => $total_items_count,
                "page_actual" => $page,
                "items_per_page" => $items_per_page,
                "total_pages" => ceil($total_items_count / $items_per_page),
                "data" => [
                    'user' => $user,
                    'videos' => $pagination
                ]
            ];

        }else{

            $data = [
                "status" => "Error",
                'code' => 400,
                'msg' => "User not found"
            ];
        }

        return $helpers->json($data);
    }
}
