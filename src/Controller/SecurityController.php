<?php

namespace App\Controller;

use App\Form\LoginType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller used for security matters.
 *
 * @author Mathieu Muller <mathieu.muller1006@gmail.com>
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, null, ['last_username' => $authUtils->getLastUsername()]);

        if ($form->isSubmitted()) {
            if ($error = $authUtils->getLastAuthenticationError()) {
                $form->addError($error);
            }
        }

        return $this->render('security/login.inc.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/sign_up", name="josmanoa_security_sign_up")
     */
    public function signUp(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, SessionInterface $session, TokenStorageInterface $tokenStorage)
    {
        $user = $em->getRepository('App:User')->createNew();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $encodedPassword = $encoder->encodePassword($user, $user->getPassword(), null);
                $user->setPassword($encodedPassword);
                $em->persist($user);
                $em->flush();

                $this->authenticate($user, $session, $tokenStorage);
                $this->addFlash('success', 'Welcome home '.$user->getUsername());

                return $this->redirectToRoute('josmanoa_index_album');
            } catch (\Exception $e) {
                dump($e);
                die;
                $this->addFlash('error', 'Error occured.');
            }
        }

        return $this->render('security/sign_up.html.twig', ['form' => $form->createView()]);
    }

    private function authenticate(UserInterface $user, SessionInterface $session, TokenStorageInterface $tokenStorage)
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $tokenStorage->setToken($token);
        $session->set('_security_main', serialize($token));
    }
}
