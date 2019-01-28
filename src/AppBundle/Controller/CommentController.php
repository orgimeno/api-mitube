<?php

namespace AppBundle\Controller;

use BackendBundle\Entity\Comment;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{

    /**
     * Create Comment
     *
     * @param Request $request
     * @return array
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

                $user_id =($identity->sub != null) ? $identity->sub : null;
                $video_id = (isset($params->video_id)) ? $params->video_id : null;
                $body = (isset($params->body )) ? $params->body : null;

                if($user_id != null && $video_id != null){
                    /** @var User $user */
                    $user = $em->getRepository(User::class)->find($identity->sub);

                    $video = $em->getRepository(Video::class)->findOneBy(['id' => $video_id]);

                    if(is_object($video)){

                        $comment = new Comment();

                        $comment->setCreatedAt($created_at);
                        $comment->setUser($user);
                        /** @var Video $video */
                        $comment->setVideo($video);
                        $comment->setBody($body);

                        $em->persist($comment);
                        $em->flush();

                        $data = [
                            'status' => "success",
                            'code' => 200,
                            'data' => $comment
                        ];
                    }else{
                        $data = [
                            'status' => "error",
                            'code' => 404,
                            'msg' => "Video no encontrado"
                        ];
                    }
                }else{
                    $data = [
                        'status' => "error",
                        'code' => 460,
                        'msg' => "Comment not created"
                    ];
                }

            }else{
                $data = [
                    'status' => "error",
                    'code' => 470,
                    'msg' => "Params failed, comment not created"
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
     * Delete Comment
     *
     * @param Request $request
     * @param null $id
     * @return array
     */
    public function deleteAction(Request $request, $id = null){
        $helpers = $this->get('app.helpers');
        $hash = $request->get('authorization', null);
        $authCheck = $helpers->authCheck($hash);

        if($authCheck){
            $em = $this->getDoctrine()->getManager();
            $identity = $helpers->authCheck($hash, true);

            $user_id =($identity->sub != null) ? $identity->sub : null;
            $comment = $em->getRepository(Comment::class)->find($id);

            if(isset($user_id) &&
                ($user_id == $comment->getUser()->getId() ||
                    $user_id == $comment->getVideo()->getUser->getId())){

                $em->remove($comment);
                $em->flush();

                $data = [
                    'status' => "success",
                    'code' => 200,
                    'msg' => "Borrado correctamente"
                ];
            }else{
                $data = [
                    'status' => "error",
                    'code' => 403,
                    'msg' => "No hga podido acceder al contenido"
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
     * List Comments
     *
     * @param null $id
     * @return array
     */
    public function listAction($id = null){
        $helpers = $this->get('app.helpers');

        $em = $this->getDoctrine()->getManager();

        if($id != null){

            $video = $em->getRepository(Video::class)->find($id);

            $comments = $em->getRepository(Comment::class)->findBy([
                'video' => $video
            ],
            [
                'id' => "desc"
            ]);

            if(count($comments) >= 1){
                $data = [
                    'status' => "success",
                    'code' => 200,
                    'data' => $comments
                ];
            }else{
                $data = [
                    'status' => "error",
                    'code' => 402,
                    'msg' => "No comments found in this video"
                ];
            }

        }else{
            $data = [
                'status' => "error",
                'code' => 402,
                'msg' => "Videos not found"
            ];
        }

        return $helpers->json($data);
    }
}
