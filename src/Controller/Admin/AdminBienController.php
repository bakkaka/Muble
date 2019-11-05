<?php

namespace App\Controller\Admin;

use App\Entity\Bien;
use App\Form\BienType;
use App\Repository\BienRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBienController extends AbstractController
{

    /**
     * @var BienRepository
     */
    private $bienRepository;
    /**
     * @var ObjectManager
     */
    private $em;
	
	 /**
     * @var \Swift_Mailer
     */
     private $mailer;

    public function __construct(BienRepository $bienRepository, ObjectManager $em, \Swift_Mailer $mailer)
    {
        $this->bienRepository = $bienRepository;
        $this->em = $em;
		$this->mailer = $mailer;
    }

    /**
     * @Route("/admin/bien", name="admin_bien_index")
     * @return \Symfony\Component\HttpFoundation\Response
	 * @IsGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $biens = $this->bienRepository->findAll();
        return $this->render('admin/bien/index.html.twig', compact('biens'));
    }

    /**
     * @Route("/admin/bien/new", name="admin_bien_new")
	  * @IsGranted("ROLE_USER")
     */
    public function new(Request $request)
    {
        $bien = new Bien();
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($bien);
            $this->em->flush();
            $this->addFlash('success', 'Bien créé avec succès');
            return $this->redirectToRoute('admin_bien_edit', ['id' => $bien->getId()]);
        }

        return $this->render('admin/bien/new.html.twig', [
            'bien' => $bien,
            'form'     => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/bien/{slug}/edit", name="admin_bien_edit", methods="GET|POST")
     * @param Bien $bien
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Bien $bien, Request $request, $slug)
    {
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
			//Envoi de message de confirmation aprés validation par l'équipe
			$message = (new \Swift_Message())//('Agence : ' .$property()->getTitle()))
            ->setFrom('abdoubakka@gmail.com')
            ->setTo($bien->getUser()->getEmail())
            //->setReplyTo($contact->getEmail())
            ->setBody($this->renderView('emails/confirm.html.twig', [ 
                'bien' => $bien
            ]), 'text/html')
			;
          $this->mailer->send($message);
			$this->addFlash('success', 'Bien modifié avec succès');
			
            return $this->redirectToRoute('admin_bien_edit', ['slug' => $bien->getSlug()]);
        }

        return $this->render('admin/bien/edit.html.twig', [
            'bien' => $bien,
            'form'     => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/bien/{slug}/delete", name="admin_bien_delete", methods="GET|POST|DELETE")
     * @param Bien $bien
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Bien $bien, Request $request, $slug, \Swift_Mailer $mailer) {
        //if ($this->isCsrfTokenValid('delete' . $bien->getId(), $request->get('_token'))) {
            $this->em->remove($bien);
            $this->em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
        //}
        return $this->redirectToRoute('admin_bien_index');
    }

}
