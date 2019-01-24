<?php

namespace AppBundle\Controller;

use BackendBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
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
}
