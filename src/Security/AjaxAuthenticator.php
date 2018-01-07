<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class AjaxAuthenticator extends AbstractGuardAuthenticator
{
    const CSRF_LOGIN_ID = 'josmanoa_login';

    protected $csrfManager;
    protected $encoderFactory;
    protected $router;

    public function __construct(CsrfTokenManagerInterface $csrfManager, EncoderFactoryInterface $encoderFactory, RouterInterface $router)
    {
        $this->csrfManager = $csrfManager;
        $this->encoderFactory = $encoderFactory;
        $this->router = $router;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return $request->isXmlHttpRequest()
            && $request->get('_username')
            && $request->get('_password')
            && $request->get('_token')
        ;
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->get('_username'),
            'password' => $request->get('_password'),
            'csrf' => $request->get('_token'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // if a User object, checkCredentials() is called
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // first check that csrf is valid
        $csrf = new CsrfToken(self::CSRF_LOGIN_ID, $credentials['csrf']);
        if ($this->csrfManager->isTokenValid($csrf)) {
            $encoder = $this->encoderFactory->getEncoder($user);

            return $encoder->isPasswordValid($user->getPassword(), $credentials['password'], false);
        } else {
            throw new InvalidCsrfTokenException();
        }

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $data = [
            'message' => 'Welcome',
            'referer' => $request->server->get('HTTP_REFERER') ?? $this->router->generate('josmanoa_home'),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required',
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
