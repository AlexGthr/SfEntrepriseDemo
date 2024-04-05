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

    // Method pour afficher la liste des entreprises
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(EntrepriseRepository $entrepriseRepository): Response
    {
        // On récupère la liste des entreprises
        $entreprises = $entrepriseRepository->findBy([], ['raisonSociale' => 'ASC']);

        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises
        ]);
    }

    // Method pour AJOUTER ou EDIT une entreprise
    #[Route('/entreprise/new', name: 'new_entreprise')]
    #[Route('/entreprise/{id}/edit', name: 'edit_entreprise')]
    public function new_edit(Entreprise $entreprise = null, Request $request, EntityManagerInterface $entityManager): Response 
    {
        // Si il n'y a pas d'entreprise,
        if (!$entreprise) {
            // On crée un nouvel objet Entreprise
            $entreprise = new Entreprise();
        }


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
            'formAddEntreprise' => $form,
            'edit' => $entreprise->getId()
        ]);
    }

    // Method pour delete une entreprise
    #[Route('/entreprise/{id}/delete', name: 'delete_entreprise')]
    public function delete(Entreprise $entreprise, EntityManagerInterface $entityManager)
    {
        // Permet la suppression de l'entreprise (delete from)
        $entityManager->remove($entreprise);
        $entityManager->flush();

        // Puis on redirige l'user vers la liste des entreprises
        return $this->redirectToRoute('app_entreprise');
    }
    
    // Method pour afficher le detail d'une entreprise
    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response 
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise
        ]);
    }

}
