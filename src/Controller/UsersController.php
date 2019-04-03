<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\Query;
use App\Entity\Users;
use App\lib\ContentResponse;
use App\lib\Auth;

class UsersController extends AbstractController
{

    /**
     * @Route("/v1/users", name="user_list", methods={"GET"})
     *
     * Returns JsonResponse with all data of the User entity.
     *
     * @return Json of the query result; according
     *         with resulset of the database.
     */

    public function list(Request $request, ContentResponse $ContentResponse)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('u')->from('App\Entity\Users', 'u');
        $query = $qb->getQuery();
        $users = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $ContentResponse->response(200, $users, $request->get('contentType'));
    }


    /**
     * @Route("/v1/users/search", name="user_search", methods={"GET"})
     *
     * Retrive data from the database, using a Doctrine qery builder object Users class.
     * It can filter the data if the $param Parameter have a "valid json string: '{"name":"Frank",...}'; also can order
     * using the same json rule string format: '{"name":"asc",...}'. Use like opertor.
     * to compare.
     *
     * @param String $param. Parameters of fields and values to filter the data.
     * @param String $order. Parameters of fields and values to order the data.
     *
     * @return Array of Collection of Users object; according with resulset of the database.
     */

    public function search(Request $request, ContentResponse $ContentResponse)
    {
        $em = $this->getDoctrine()->getManager();

        $c = array();

        $q = $em->createQueryBuilder();
        $q->select('u')->from('App\Entity\Users', 'u');

        $param =  is_null(json_decode($request->get('param'), true))? array() : json_decode($request->get('param'), true);
        $order =  is_null(json_decode($request->get('order'), true))? array() : json_decode($request->get('order'), true);

        foreach ($param as $key => $value) {
            $c[] = 'u.'.$key." like '%".$value."%'";
        }

        if (count($c)>0) {
            $orX = $q->expr()->orX();
            $orX->addMultiple($c);
            $q->where($orX);
        }

        foreach ($order as $key => $value) {
            $q->addOrderBy('u.'.$key, $value);
        }

        $users = $q->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $ContentResponse->response(200, $users, $request->get('contentType'));
    }

    /**
     ** @Route("/v1/users/{id}", name="user_find", methods={"GET"})
     *
     * Action thats find a user record.
     *
     * @param Integer $id
     */

    public function find($id, Request $request, ContentResponse $ContentResponse)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(Users::class)->find($id);

        if ($user) {
            return $ContentResponse->response(200, ["id" => $user->getId(), "name" => $user->getName(),"lastname" => $user->getLastName(),"email" => $user->getEmail(),"apiToken" => $user->getApiToken()], $request->get('contentType'));
        }
        return $ContentResponse->response(404, 'No User found', $request->get('contentType'));
    }


    /**
     * @Route("/v1/users", name="user_create", methods={"POST"})
     *
     *
     * Action thats create a new User record.
     *
     * @param Request $request. Instance of request object.
     *
     * @return Json reponse with a message for successfull/unsuccessfull.
     */

    public function create(Request $request, ContentResponse $ContentResponse, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        if (count($em->getRepository(Users::class)->findBy(['email' => $request->get('email')]))>0) {
            return $ContentResponse->response(500, 'Already exist an user with this email: '.$request->get('email'), $request->get('contentType'));
        }

        $user = new Users();
        $user->setName($request->get('name'));
        $user->setLastName($request->get('lastname'));
        $user->setEmail($request->get('email'));
        $rol = json_decode($request->get('roles'), true);
        $user->setRoles($rol);
        $user->setPassword($passwordEncoder->encodePassword($user, $request->get('password')));
        $user->setApiToken(Auth::SignIn(['email' => $request->get('email'),'id' => rand(0, 9999),]));
        $em->persist($user);

        $em->flush();

        $body ='User created successfully!';
        $code = 201;

        if ($user->getId()===null) {
            $body ='Can\'t create the user';
            $code = 500;
        }

        return $ContentResponse->response($code, $body, $request->get('contentType'), $user->getId());
    }


    /**
     * @Route("/v1/users/{id}", name="user_update", methods={"PUT","PATCH"})
     *
     * Action thats update a User record.
     *
     * @param Integer $id. Mandatory parameter contents the Pk value
     *        of the table.
     *
     * @param Request $request. Instance of request object.
     *
     * @return Json reponse with a message for successfull.
     */

    public function update($id, Request $request, ContentResponse $ContentResponse, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->find($id);

        $body = 'No User found for id '.$id;
        $code = 404;

        if ($user) {
            $user->setName($request->get('name')??$user->getName());
            $user->setLastName($request->get('lastname')??$user->getLastName());
            $user->setEmail($request->get('email')??$user->getEmail());
            $rol = json_decode($request->get('roles'), true);
            $user->setRoles($rol??$user->getRoles());
            $user->setPassword($passwordEncoder->encodePassword($user, $request->get('password')));
            $em->persist($user);
            $em->flush();

            $body ='User updated successfully!';
            $code = 200;
        }

        return $ContentResponse->response($code, $body, $request->get('contentType'));
    }


    /**
     * @Route("/v1/users/{id}", name="user_delete", methods={"DELETE"})
     *
     * Action thats delete a User record.
     *
     * @param Integer $id. Mandatory parameter contents the Pk value
     *        of the table.
     *
     * @return Json reponse with a message for successfull.
     */

    public function delete($id, Request $request, ContentResponse $ContentResponse)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->find($id);

        $body = 'No User found for id '.$id;
        $code = 404;

        if ($user) {
            try {
                $em->remove($user);
                $em->flush();

                $body ='User deleted successfully!';
                $code = 200;
            } catch (\Exception $e) {
                return $ContentResponse->response(500, $e->getMessage(), $request->get('contentType'));
            }
        }

        return $ContentResponse->response($code, $body, $request->get('contentType'));
    }
}
