<?php

namespace Lightning\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use FOS\RestBundle\Controller\Annotations\View;

use Lightning\ApiBundle\Entity\Account;
use Lightning\ApiBundle\Entity\AccessToken;

/**
 * Controller for access tokens.
 */
class AccessTokenController
{
    protected $manager;

    protected $router;

    /**
     * @InjectParams({
     *     "manager" = @Inject("lightning.api_bundle.service.access_token_manager"),
     *     "router" = @Inject("router"),
     * })
     */
    public function __construct($manager, $router)
    {
        $this->manager = $manager;
        $this->router = $router;
    }

    /**
     * @Route("/{id}/{code}.{_format}", requirements={"id" = "\d+"}, defaults={"_format" = "html"})
     * @Method("GET")
     * @View()
     */
    public function accessAction($id, $code)
    {
        return array('id' => $id, 'code' => $code);
    }

    /**
     * @Route("/{id}/{code}.{_format}", requirements={"id" = "\d+"}, defaults={"_format" = "json"})
     * @Method("POST")
     * @View()
     */
    public function accessTokenAction($id, $code)
    {
        $token = $this->manager->createAccessToken($id, $code);

        return $token;
    }

    /**
     * @Route("/accounts/{accountId}/access_tokens/{tokenId}.{_format}",
     *     requirements={"accountId" = "\d+", "id" = "\d+"}, defaults={"_format" = "json"})
     * @Method("PUT")
     * @View(statusCode=204)
     */
    public function approveAction($accountId, $tokenId, Request $request)
    {
        $challenge = $request->get('challenge');

        $this->manager->approveAccessToken($accountId, $tokenId, $challenge);
    }
}
