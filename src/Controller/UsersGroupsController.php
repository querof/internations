<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use App\Entity\Users;
use App\Entity\Groups;
use App\Entity\UsersGroups;
use App\lib\ContentResponse;

class UsersGroupsController extends AbstractController
{

      /**
       * @Route("/v1/user/groups", name="user_group_list", methods={"GET"})
       *
       * Returns JsonResponse with all data of the User Group entity.
       *
       * @return Json of the query result; according
       *         with resulset of the database.
       */

    public function list(Request $request, ContentResponse $ContentResponse)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();
        $qb->select('u.email,g.name,ug')->from('App\Entity\UsersGroups', 'ug')->leftJoin('ug.users', 'u')->leftJoin('ug.groups', 'g');
        $query = $qb->getQuery();
        $groups = $query->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        return $ContentResponse->response(200, $groups, $request->get('contentType'));
    }


    /**
     * @Route("/v1/user/groups", name="user_group_create", methods={"POST"})
     *
     * Action thats create a new User Group relation record.
     *
     * @param Request $request. Instance of request object.
     *
     * @return Json reponse with a message for successfull/unsuccessfull.
     */

    public function create(Request $request, ContentResponse $ContentResponse)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $userGroup = $this->search($request->get('userId'), $request->get('groupId'));

        if ($userGroup) {
            return $ContentResponse->response('409', 'Group already related with this user', $request->get('contentType'));
        }

        $user = $em->getRepository(Users::class)->find($request->get('userId'));
        $group = $em->getRepository(Groups::class)->find($request->get('groupId'));

        if ($user && $group) {
            $userGroup = new UsersGroups();
            $userGroup->setUsers($user);
            $userGroup->setGroups($group);

            $em->persist($userGroup);

            $em->flush();

            $body ='Relation between User and Group created successfully!';
            $code = 201;
        }

        if ($userGroup->getId()===null) {
            $body ='Can\'t create the User Group relation';
            $code = 500;
        }

        return $ContentResponse->response($code, $body, $request->get('contentType'), $userGroup->getId());
    }


    /**
     * @Route("/v1/user/groups/{id}", name="user_group_delete", methods={"DELETE"})
     *
     * Action thats delete a Group record.
     *
     * @param Integer $userId. Mandatory parameter contents the userid.
     *
     * @param Integer $groupId. Mandatory parameter contents the groupid.
     *
     * @return Json reponse with a message for successfull.
     */

    public function delete($id, Request $request, ContentResponse $ContentResponse)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $body = 'No User Group found';
        $code = 404;

        $userGroup = $em->getRepository(UsersGroups::class)->find($id);

        if ($userGroup) {
            $em->remove($userGroup);
            $em->flush();

            $body ='User Group deleted successfully!';
            $code = 200;
        }

        return $ContentResponse->response($code, $body, $request->get('contentType'));
    }


    /**
     *
     * Action thats find a user Group record.
     *
     * @param Integer $userId. Mandatory parameter contents the userid.
     *
     * @param Integer $groupId. Mandatory parameter contents the groupid.
     *
     * @return UsersGroups object.
     */

    private function search($userId, $groupId)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(Users::class)->find($userId);
        $group = $em->getRepository(Groups::class)->find($groupId);

        $userGroup = [];

        $userGroup = $em->getRepository(UsersGroups::class)->findBy(array('users' => $user,'groups' => $group));

        return  count($userGroup)>0?$userGroup[0]:false;
    }
}
