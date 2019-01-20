<?php

namespace AppBundle\Services;

use BackendBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Firebase\JWT\JWT;

class JwtAuth
{

    public $manager;
    public $key;

    /**
     * JwtAuth constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->key = "clave_secreta";
    }

    /**
     * @param $data
     * @return array
     */
    public function signup($email, $password, $getHash = null)
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email,
            'password' => $password
        ]);

        $signup = false;

        if($user){
            $signup = true;
        }

        if($signup){

            $token = [
                'sub' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'password' => $user->getPassword(),
                'image' => $user->getImage(),
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            ];

            $exp = time() + (7 * 24 * 60 * 60);

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            return $getHash != null ? $jwt : $decoded;
        }else{
            return [
                'status' => 'error',
                'data' => "Login failed"
            ];
        }
    }

    public function checkToken($jwt, $getIdentity = true)
    {
        $key = $this->key;
        $auth = false;

        $decode = null;

        try{
            $decode = JWT::decode($jwt, $key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch (\DomainException $d){
            $auth = false;
        }

        // If decoded object has the property is logged
        if(isset($decode->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity == true){
            return $decode;
        }else{
            return $auth;
        }

    }
}