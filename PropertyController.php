<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Bien;
use App\Form\BienType;
use App\Repository\BienRepository;
use App\Entity\PropertySearch;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Notification\ContactNotification;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BienController extends AbstractController
{
    
	
	 /**
     * @var \Swift_Mailer
     */
     private $mailer;




    /**
     * @var BienRepository
     */
    private $bienRepository;
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(BienRepository $bienRepository, \Swift_Mailer $mailer, ObjectManager $em)
    {
        $this->bienRepository = $bienepository;
        $this->em = $em;
		$this->mailer = $mailer;
    }

    /**
     * @Route("/biens", name="property_index")
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->bienRepository->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('bien/index.html.twig', [
            'current_menu' => 'biens',
            'biens'   => $biens,
            'form'         => $form->createView()
        ]);
    }

    /**
     * @Route("/biens/{slug}", name="bien_show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Bien $bien
     * @return Response
     */
    public function show(Bien $bien, PropertyRepository $propertyRepository, string $slug, Request $request, ContactNotification $notification): Response
    {
        if ($bien->getSlug() !== $slug) {
            return $this->redirectToRoute('bien_show', [
                'id'   => $bien->getId(),
                'slug' => $bien->getSlug()
            ], 301);
        }

        $contact = new Contact();
        $contact->setBien($bien);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé');
            return $this->redirectToRoute('property.show', [
                'id'   => $bien->getId(),
                'slug' => $bien->getSlug()
            ]);
        }

        return $this->render('bien/show.html.twig', [
            'bien'     => $bien,
            'current_menu' => 'biens',
            'form'         => $form->createView()
        ]);
    }
	
	/**
     * @Route("/bien/new", name="bien_new")
	 * @IsGranted("ROLE_USER")
     */
    public function new(BienRepository $bienRepository, Request $request, \Swift_Mailer $mailer)
    {
	
	    $user = $this->getUser();
		 //$author = $user->getAuthor();
	     //$user = $this->user->getAuthor();
		//if(!$user->getAuthor()){
		//  $this->addFlash('error', 'Inscrivez-vous pour devenir un auteur!');
       // return $this->redirectToRoute('admin_author_new');
		
		//}
	
	    
        $bien = new Bien();
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
		 $bien->setUser($user);
            $this->em->persist($bien);
            $this->em->flush();
			//$property = $propertyRepository->findOneBySlug($slug);
			
			$message = (new \Swift_Message())//('Agence : ' .$property()->getTitle()))
            ->setFrom('abdoubakka@gmail.com')
            ->setTo($bien->getUser()->getEmail())
            //->setReplyTo($contact->getEmail())
            ->setBody($this->renderView('emails/contact.html.twig', [ 
                'bien' => $bien
            ]), 'text/html')
			;
          $this->mailer->send($message);
    

            $this->addFlash('success', 'Bien créé avec succès');
            return $this->redirectToRoute('admin_bien_edit', ['id' => $bien->getId()]);
        }

        return $this->render('admin/bien/new.html.twig', [
            'bien' => $bien,
            'form'     => $form->createView()
        ]);
    }

    /**
     * @Route("/bien/{id}/edit", name="bien_edit", methods="GET|POST")
     * @param Bien $bien
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Bien $bien, Request $request, $id)
    {
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('admin_bien_edit', ['id' => $bien->getId()]);
        }

        return $this->render('admin/bien/edit.html.twig', [
            'bien' => $bien,
            'form'     => $form->createView()
        ]);
    }

    /**
     * @Route("/bien/{id}/delete", name="bien_delete", methods="GET|POST|DELETE")
     * @param Bien $bien
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Bien $bien, Request $request, $id): Response
	{
        //if ($this->isCsrfTokenValid('delete' . $property->getId(), $request->get('_token'))) {
            $this->em->remove($bien);
            $this->em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
        
       
		//}
		 return $this->redirectToRoute('bien_list');
		
		// return $this->render('admin/property/_delete_form.html.twig', [
         //   'property' => $property,
            //'form'     => $form->createView()
        //]);
    }
	
	/**
     * @Route("/list/", name="bien_list")
	  * @IsGranted("ROLE_USER")
     */
    public function list(BienRepository $bienRepository)
    {
	     $user = $this->getUser();
		
       
        return $this->render('bien/list.html.twig', [
            'allbiens' => $propertyRepository->getBiensWithUser($user),
        ]);
    }

	

}
