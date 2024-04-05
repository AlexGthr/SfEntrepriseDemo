<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeController extends AbstractController
{
        // On défini une route pour l'accès à la method
    #[Route('/employe', name: 'app_employe')]
    public function index(EmployeRepository $employesRepository): Response
    {
        // On recupère les employes pour les afficher
        $employes = $employesRepository->findBy([], ['nom' => 'ASC']);

        return $this->render('employe/index.html.twig', [
            'employes' => $employes
        ]);
    }

        // Pour un formulaire, on peux utiliser la même method pour AJOUTER ou EDIT un objet
    #[Route('/employe/new', name: 'new_employe')]
    #[Route('/employe/{id}/edit', name: 'edit_employe')]
    public function new(Employe $employe = null, Request $request, EntityManagerInterface $entityManager): Response 
    {
        // Si il n'y a pas d'employé, alors on crée un nouveau
        if (!$employe) {
            $employe = new Employe();
        }
        
        // On crée le formulaire
        $form = $this->createForm(EmployeType::class, $employe);

        $form->handleRequest($request);
        

        // Si le formulaire est submit
        if ($form->isSubmitted() && $form->isValid()) {
            
            // On recupère les données du formulaire
            $employe = $form->getData();

            // PREPARE PDO
            $entityManager->persist($employe);
            // EXECUTE PDO
            $entityManager->flush();

            // Puis on redirige l'user vers la liste des entreprises
            return $this->redirectToRoute('app_employe');
        }
        
        return $this->render('employe/new.html.twig', [
            'formAddEmploye' => $form,
            'edit' => $employe->getId()
        ]);
    }

    // Method pour supprimer un employé
    #[Route('/employe/{id}/delete', name: 'delete_employe')]
    public function delete(Employe $employe, EntityManagerInterface $entityManager)
    {
        // Permet la suppression d'un employé (delete from)
        $entityManager->remove($employe);
        $entityManager->flush();

        // Puis on redirige l'user vers la liste des entreprises
        return $this->redirectToRoute('app_employe');
    }

    // Method pour afficher le detail d'un employé
    #[Route('/employe/{id}', name: 'show_employe')]
    public function show(Employe $employe): Response 
    {
        return $this->render('employe/show.html.twig', [
            'employe' => $employe
        ]);
    }
}
