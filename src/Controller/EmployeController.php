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
    #[Route('/employe', name: 'app_employe')]
    public function index(EmployeRepository $employesRepository): Response
    {
        $employes = $employesRepository->findBy([], ['nom' => 'ASC']);
        return $this->render('employe/index.html.twig', [
            'employes' => $employes
        ]);
    }

    #[Route('/employe/new', name: 'new_employe')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response 
    {
        $employe = new Employe();
        
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
            'formAddEmploye' => $form
        ]);
    }

    #[Route('/employe/{id}', name: 'show_employe')]
    public function show(Employe $employe): Response 
    {
        return $this->render('employe/show.html.twig', [
            'employe' => $employe
        ]);
    }
}
