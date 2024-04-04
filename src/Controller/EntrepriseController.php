<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {

        $entreprises = $entrepriseRepository->findBy([], ['raisonSociale' => 'ASC']);
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises
        ]);
    }

    
    #[Route('/entreprise/new', name: 'new_entreprise')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response 
    {
        // On crée un nouvel objet Entreprise
        $entreprise = new Entreprise();

        // On crée le formulaire pour l'entreprise
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        
        $form->handleRequest($request);
        

        // Si le formulaire est submit
        if ($form->isSubmitted() && $form->isValid()) {
            
            // On recupère les données du formulaire
            $entreprise = $form->getData();

            // PREPARE PDO
            $entityManager->persist($entreprise);
            // EXECUTE PDO
            $entityManager->flush();

            // Puis on redirige l'user vers la liste des entreprises
            return $this->redirectToRoute('app_entreprise');
        }
        
        return $this->render('entreprise/new.html.twig', [
            'formAddEntreprise' => $form
        ]);
    }
    
    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response 
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise
        ]);
    }

}
