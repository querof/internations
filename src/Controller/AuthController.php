<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Security\TokenAuthenticator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\lib\ContentResponse;
use App\Entity\Users;
use App\lib\Auth;

class AuthController extends AbstractController
{
    /**
     * @Route("/v1/login", name="app_login", methods={"GET"})
     *
     * Generate Token.
     *
     * @param Request $request. Instance of request object. with the parameter
     *        "params" in json format with the fields and values filter to make
     *        the search.
     *
     * @return String with Token.
     */

    public function SignInAction(TokenAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler, Request $request, ContentResponse $ContentResponse, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Users::class)->findOneBy(['email' => $request->get('username')]);


        if ($passwordEncoder->isPasswordValid($user, $request->get('password'))) {
            $user->setApiToken(Auth::SignIn(['email' => $request->get('email'),'id' => rand(0, 9999),]));

            $em->persist($user);

            $em->flush();

            $guardHandler->authenticateUserAndHandleSuccess(
              $user,          // the User object you just created
              $request,
              $authenticator, // authenticator whose onAuthenticationSuccess you want to use
              'main'          // the name of your firewall in security.yaml
          );

            return $ContentResponse->response(200, $user->getApiToken(), $request->get('contentType'));
        }

        return $ContentResponse->response(401, 'Wrong user name or password', $request->get('contentType'));
    }


    /**
     * Check the Token.
     *
     * @param Request $request. Instance of request object. conatints 'token'
     *        parameter, that containts token string.
     * @param Boolean true if token it's valid.
     *
     * @return Json of the query result; according
     *         with resulset of the database.
     */

    public function CheckAction(Request $request)
    {
        return new JsonResponse(array('token' => Auth::Check($request->get('token'))));
    }
}
