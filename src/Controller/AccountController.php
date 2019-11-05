<?php

namespace App\Controller;


use App\Repository\DiscussionRepository;
use App\Repository\CommentRepository;
use App\Repository\BienRepository;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class AccountController extends BaseController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function index(LoggerInterface $logger, DiscussionRepository $discussionRepository, BienRepository $bienRepository)
    {
	    $user = $this->getUser();
		//$author = $user->getAuthor();
        $logger->debug('Checking account page for '.$this->getUser()->getEmail());
        return $this->render('account/index.html.twig', [
		 'alldiscussions' => $discussionRepository->getDiscussionWithUser($user),
		 'allbiens' => $bienRepository->getBiensWithUser($user)

        ]);
    }
	
	/**
     * @Route("/api/account", name="api_account")
     */
    public function accountApi()
    {
        $user = $this->getUser();
        return $this->json($user, 200, [], [
            'groups' => ['main'],
        ]);
    }
	
	
}
