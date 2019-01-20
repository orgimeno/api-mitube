<?php

namespace AppBundle\Controller;

use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;
use BackendBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Asserts;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * Login Action
     * @param Request $request
     */
    public function loginAction(Request $request)
    {
        $helpers = $this->get('app.helpers');
        $jwtAuth = $this->get('app.jwtAuth');

        // Get json by post
        $json = $request->get('json', null);

        if($json !== null){
            $params = json_decode($json);
            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $getHash = (isset($params->gethash)) ? $params->gethash : null;

            $emConstrait = new Asserts\Email();
            $emConstrait->message = "This email is not valid";
            $validate_mail = $this->get('validator')->validate($email, $emConstrait);

            // Cifrado pass

            $pwd = hash('sha256', $password);

            if(count($validate_mail) == 0 && $password != null){

                if($getHash == null){

                    $signup = $jwtAuth->signup($email, $pwd);

                }else{
                    $signup = $jwtAuth->signup($email, $pwd, true);
                }

                return new JsonResponse($signup);

            }else{
                return $helpers->json([
                    'status' => 'error',
                    'data' => 'Login not valid'
                ]);
            }

        }else{
            return $helpers->json([
                'status' => 'error',
                'data' => 'Send Json with post'
            ]);
        }
    }

    /**
     * @Route("/pruebas", name="pruebas")
     * @param Request $request
     * @return void
     */
    public function pruebasAction(Request $request)
    {
        $helpers = $this->get('app.helpers');
//      Send Authorization by headers but if fails use POST

//      $hash = $request->get('authorization', null);
        $hash = $request->headers->get('authorization');
        /** @var JwtAuth $jwtAuth */
        $check = $helpers->authCheck($hash, true);

        var_dump($check);die();
    }
}
