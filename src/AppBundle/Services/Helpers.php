<?php

    namespace AppBundle\Services;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Serializer\Encoder\JsonEncoder;
    use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
    use Symfony\Component\Serializer\Serializer;

    class Helpers
    {

        public $jwt_auth;

        public function __construct($jwt_auth)
        {
            $this->jwt_auth = $jwt_auth;
        }

        public function authCheck($hash, $getId = false)
        {
            $jwt_auth = $this->jwt_auth;

            $auth = false;

            if ($hash !== null){
                if($getId == false){
                    $check_token = $jwt_auth->checkToken($hash);
                    if($check_token == true){
                        $auth = true;
                    }

                }else{
                    $check_token = $jwt_auth->checkToken($hash, true);
                    if(is_object($check_token)){
                        $auth = $check_token;
                    }
                }
            }

            return $auth;

        }

        /**
         * @param $data
         * @return Response
         */
        public function json($data)
        {
            $normalizer = array(new GetSetMethodNormalizer());

            $encoders = array("json" => new JsonEncoder());
            $seriealizer = new Serializer($normalizer, $encoders);

            $json = $seriealizer->serialize($data, 'json');

            $response = new Response();
            $response->setContent($json);
            $response->headers->set("content-type", "application/json");

            return $response;

        }
    }