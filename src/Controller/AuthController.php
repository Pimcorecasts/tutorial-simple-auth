<?php

namespace App\Controller;

use App\Model\DataObject\User;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/auth', name: 'simple_auth_')]
class AuthController extends FrontendController
{
    #[Route('/login', name: 'login')]
    public function defaultAction(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        UserInterface $user = null
    ): Response
    {
        if( $user && $this->isGranted( 'ROLE_USER' ) ){
            return $this->redirectToRoute( 'dashboard' );
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/register', name: 'register')]
    public function registerAction( Request $request ): Response{

        if( $request->get('submit') ){
            $password = $request->get('password');
            $confirmPassword = $request->get('passwordConfirm');

            if( $password === '' || $confirmPassword === ''){
                $this->addFlash('error', 'Password and/or Password Confirm cannot be empty');
            }

            if( $password !== $confirmPassword ){
                $this->addFlash('error', 'Passwords do not match');
            }

            $user = User::getByUsername($request->get('email'), 1);
            if( $user instanceof User){
                $this->addFlash('error', 'User already exists');
            }

            if( !$request->getSession()->getFlashBag()->has('error') ){
                $user = new User();
                $user->setParentId(7);
                $user->setPublished(true);
                $user->setKey( Service::getValidKey( $request->get('email'), 'object' ) );
                $user->setUsername($request->get('email'));
                $user->setPassword($request->get('password'));
                $user->save();

                $this->addFlash('success', 'User registered successfully');
            }
            return $this->redirectToRoute('simple_auth_login');
        }

        return $this->render('Auth/register.html.twig');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(){
        return $this->redirectToRoute('simple_auth_login');
    }

}
