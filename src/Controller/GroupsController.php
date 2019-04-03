<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use App\Entity\Groups;
use App\lib\ContentResponse;

class GroupsController extends AbstractController
{
    /**
     * @Route("/v1/groups", name="group_list", methods={"GET"})
     *
     * Returns JsonResponse with all data of the Group entity.
     *
     * @return Json of the query result; according
     *         with resulset of the database.
     */

    public function list(Request $request, ContentResponse $ContentResponse)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('g')->from('App\Entity\Groups', 'g');
        $query = $qb->getQuery();
        $groups = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $ContentResponse->response(200, $groups, $request->get('contentType'));
    }


    /**
     * @Route("/v1/groups/search", name="group_search", methods={"GET"})
     *
     * Retrive data from the database, using a Doctrine qery builder object Groups class.
     * It can filter the data if the $param Parameter have a "valid json string: '{"name":"Frank",...}'; also can order
     * using the same json rule string format: '{"name":"asc",...}'. Use like opertor.
     * to compare.
     *
     * @param String $param. Parameters of fields and values to filter the data.
     * @param String $order. Parameters of fields and values to order the data.
     *
     * @return Array of Collection of Groups object; according with resulset of the database.
     */

    public function search(Request $request, ContentResponse $ContentResponse)
    {
        $em = $this->getDoctrine()->getManager();

        $c = array();

        $q = $em->createQueryBuilder();
        $q->select('g')->from('App\Entity\Groups', 'g');

        $param =  is_null(json_decode($request->get('param'), true))? array() : json_decode($request->get('param'), true);
        $order =  is_null(json_decode($request->get('order'), true))? array() : json_decode($request->get('order'), true);

        foreach ($param as $key => $value) {
            $c[] = 'g.'.$key." like '%".$value."%'";
        }

        if (count($c)>0) {
            $orX = $q->expr()->orX();
            $orX->addMultiple($c);
            $q->where($orX);
        }

        foreach ($order as $key => $value) {
            $q->addOrderBy('g.'.$key, $value);
        }

        $groups = $q->getQuery()->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $ContentResponse->response(200, $groups, $request->get('contentType'));
    }


    /**
     ** @Route("/v1/groups/{id}", name="groups_find", methods={"GET"})
     *
     * Action thats find a user record.
     *
     * @param Integer $id
     */

    public function find($id, Request $request, ContentResponse $ContentResponse)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(Groups::class)->find($id);

        if ($group) {
            return $ContentResponse->response(200, ["id" => $group->getId(), "name" => $group->getName(),"description" => $group->getDescription()]);
        }

        return $ContentResponse->response(404, 'No Group found', $request->get('contentType'));
    }


    /**
     * @Route("/v1/groups", name="group_create", methods={"POST"})
     *
     * Action thats create a new Group record.
     *
     * @param Request $request. Instance of request object.
     *
     * @return Json reponse with a message for successfull/unsuccessfull.
     */

    public function create(Request $request, ContentResponse $ContentResponse)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $group = new Groups();
        $group->setName($request->get('name'));
        $group->setDescription($request->get('description'));

        $em->persist($group);

        $em->flush();

        $body ='Group created successfully!';
        $code = 201;

        if ($group->getId()===null) {
            $body ='Can\'t create the Group';
            $code = 500;
        }

        return $ContentResponse->response($code, $body, $request->get('contentType'), $group->getId());
    }


    /**
     * @Route("/v1/groups/{id}", name="group_update", methods={"PUT","PATCH"})
     *
     * Action thats update a Group record.
     *
     * @param Integer $id. Mandatory parameter contents the Pk value
     *        of the table.
     *
     * @param Request $request, ContentResponse $ContentResponse. Instance of request object.
     *
     * @return Json reponse with a message for successfull.
     */

    public function update($id, Request $request, ContentResponse $ContentResponse)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository(Groups::class)->find($id);

        $body = 'No Group found for id '.$id;
        $code = 404;

        if ($group) {
            $group->setName($request->get('name')??$group->getName());
            $group->setDescription($request->get('description')??$group->getDescription());

            $em->persist($group);
            $em->flush();

            $body ='Group updated successfully!';
            $code = 200;
        }

        return $ContentResponse->response($code, $body, $request->get('contentType'));
    }


    /**
     * @Route("/v1/groups/{id}", name="group_delete", methods={"DELETE"})
     *
     * Action thats delete a Group record.
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
        $group = $em->getRepository(Groups::class)->find($id);

        $body = 'No Group found for id '.$id;
        $code = 404;

        if ($group) {
            try {
                $em->remove($group);
                $em->flush();
                $body ='Group deleted successfully!';
                $code = 200;
            } catch (\Exception $e) {
                return $ContentResponse->response(500, $e->getMessage(), $request->get('contentType'));
            }
        }

        return $ContentResponse->response($code, $body, $request->get('contentType'));
    }
}
