<?php

namespace AppBundle\Controller;

use BackendBundle\Entity\User;
use BackendBundle\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Asserts;

class VideoController extends Controller
{
    /**
     * Create Video
     *
     * @throws \Exception
     */
    public function newAction(Request $request){
        $helpers = $this->get('app.helpers');
        $hash = $request->get('authorization', null);
        $authCheck = $helpers->authCheck($hash);

        if($authCheck){
            $em = $this->getDoctrine()->getManager();
            $identity = $helpers->authCheck($hash, true);

            $json = $request->get("json", null);
            if($json != null){

                $params = json_decode($json);

                $created_at = new \DateTime('now');
                $update_at = new \DateTime('now');
                $imagen = null;
                $videos_path = null;

                $user_id =($identity->sub != null) ? $identity->sub : null;
                $title = (isset($params->title)) ? $params->title : null;
                $description = (isset($params->description )) ? $params->description : null;
                $status = (isset($params->status )) ? $params->status : null;

                if($user_id != null && $title != null){
                    /** @var User $user */
                    $user = $em->getRepository(User::class)->find($identity->sub);


                    $video = new Video();

                    $video->setCreatedAt($created_at);
                    $video->setUpdatedAt($update_at);
                    $video->setUser($user);
                    $video->setDescription($description);
                    $video->setStatus($status);
                    $video->setTitle($title);

                    $em->persist($video);
                    $em->flush();

                    $data = [
                        'status' => "success",
                        'code' => 200,
                        'data' => $video
                    ];
                }else{
                    $data = [
                        'status' => "error",
                        'code' => 460,
                        'msg' => "Video not created"
                    ];
                }

            }else{
                $data = [
                    'status' => "error",
                    'code' => 470,
                    'msg' => "Params failed, video not created"
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
     * Edit Video
     *
     * @throws \Exception
     */
    public function editAction(Request $request, $id = null){
        $helpers = $this->get('app.helpers');
        $hash = $request->get('authorization', null);
        $authCheck = $helpers->authCheck($hash);

        if($authCheck){
            $em = $this->getDoctrine()->getManager();
            $identity = $helpers->authCheck($hash, true);

            $json = $request->get("json", null);
            if($json != null){

                $params = json_decode($json);

                $update_at = new \DateTime('now');
                $imagen = null;
                $videos_path = null;

                $title = (isset($params->title)) ? $params->title : null;
                $description = (isset($params->description )) ? $params->description : null;
                $status = (isset($params->status )) ? $params->status : null;

                if($title != null){

                    $video = $em->getRepository(Video::class)->find($id);

                    if(isset($identity->sub) && $identity->sub == $video->getUser()->getId()){

                        $video->setUpdatedAt($update_at);
                        $video->setDescription($description);
                        $video->setStatus($status);
                        $video->setTitle($title);

                        $em->persist($video);
                        $em->flush();

                        $data = [
                            'status' => "success",
                            'code' => 200,
                            'msg' => "Video updated success"
                        ];
                    }else{

                        $data = [
                            'status' => "error",
                            'code' => 400,
                            'msg' => "Video not updated success"
                        ];
                    }

                }else{
                    $data = [
                        'status' => "error",
                        'code' => 460,
                        'msg' => "Video not updated"
                    ];
                }

            }else{
                $data = [
                    'status' => "error",
                    'code' => 470,
                    'msg' => "Params failed, video not updated"
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
     * @param null $id
     * @return mixed
     */
    public function uploadAction(Request $request, $id = null){

        $helpers = $this->get('app.helpers');
        $hash = $request->get('authorization', null);
        $authCheck = $helpers->authCheck($hash);

        if($authCheck){
            $em = $this->getDoctrine()->getManager();
            $identity = $helpers->authCheck($hash, true);

            if($id != null){
                $video = $em->getRepository(Video::class)->find($id);

                if($video->getUser()->getId() == $identity->sub){
                    $file = $request->files->get('image', null);
                    $file_video = $request->files->get('video', null);

                    if($file != null && !empty($file)){

                        $ext = $file->guessExtension();
                        if($ext == 'jpg' || $ext == 'gif'
                        || $ext == 'jpeg' || $ext == 'png') {
                            $file_name = time() . "." . $ext;
                            $path_of_file = "uploads/video_images/video_" . $id;
                            $file->move($path_of_file, $file_name);
                            $video->setImage($file_name);

                            $em->persist($video);
                            $em->flush();

                            $data = [
                                'status' => "success",
                                'code' => 200,
                                'msg' => "Video image uploaded",
                                'file_name' => $file_name
                            ];
                        }else{

                            $data = [
                                'status' => "error",
                                'code' => 460,
                                'msg' => "Extension not valid"
                            ];
                        }
                    }elseif($file_video != null && !empty($file_video)){

                        $ext = $file_video->guessExtension();
                        if($ext == 'avi' || $ext == 'mp4'
                        || $ext == 'mkv' || $ext == 'webm') {
                            $file_name = time() . "." . $ext;
                            $path_of_file = "uploads/video_files/video_" . $id;
                            $file_video->move($path_of_file, $file_name);
                            $video->setVideoPath($file_name);

                            $em->persist($video);
                            $em->flush();

                            $data = [
                                'status' => "success",
                                'code' => 200,
                                'msg' => "Video uploaded"
                            ];
                        }else{

                            $data = [
                                'status' => "error",
                                'code' => 460,
                                'msg' => "Extension not valid"
                            ];
                        }

                    }else{
                        $data = [
                            'status' => "error",
                            'code' => 400,
                            'msg' => "Request empty"
                        ];
                    }
                }else{
                    $data = [
                        'status' => "error",
                        'code' => 400,
                        'msg' => "Video not allowed"
                    ];
                }
            }else{
                $data = [
                    'status' => "error",
                    'code' => 400,
                    'msg' => "Video not located"
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
    public function videosAction(Request $request){

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();

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
            "data" => $pagination
        ];

        return $helpers->json($data);
    }

    /**
     * @param Request $request
     */
    public function lastsVideosAction(Request $request){

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT v from BackendBundle:Video v ORDER BY v.createdAt DESC";

        $query = $em->createQuery($dql)->setMaxResults(5);
        $video = $query->getResult();

        $data = [
            'status' => "success",
            'data' => $video
        ];

        return $helpers->json($data);
    }

    /**
     * @param Request $request
     */
    public function videoAction(Request $request, $id = null){

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();

        if(!is_null($id)){

            $video = $em->getRepository(Video::class)->find($id);

            $data = [
                'status' => "success",
                'data' => $video
            ];

        }else{

            $data = [
                'status' => "error",
                'code' => 400,
                'msg' => "Video id not selected"
            ];
        }

        return $helpers->json($data);
    }

    /**
     * @param Request $request
     */
    public function searchAction(Request $request, $search = null){

        $helpers = $this->get("app.helpers");

        $em = $this->getDoctrine()->getManager();

        if(!is_null($search)) {


            $dql = "SELECT v from BackendBundle:Video v WHERE v.title like :search OR v.description LIKE :search ORDER BY v.createdAt DESC";

            $query = $em->createQuery($dql)->setParameter('search', '%' . $search . '%');

        }else{

            $dql = "SELECT v from BackendBundle:Video v ORDER BY v.createdAt DESC";

            $query = $em->createQuery($dql);
        }

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
            "data" => $pagination
        ];

        return $helpers->json($data);
    }
}
