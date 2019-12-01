<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController.
 */
class ShowController extends AbstractController
{
    /**
     * @var UserFetcher $users User fetcher.
     */
    private $users;

    /**
     * ProfileController constructor.
     *
     * @param UserFetcher $users User fetcher.
     */
    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * @Route("/profile", name="profile")
     *
     * @return Response
     */
    public function show(): Response
    {
        $user = $this->users->findDetail($this->getUser()->getId());

        return $this->render('app/profile/show.html.twig', compact('user'));
    }
}