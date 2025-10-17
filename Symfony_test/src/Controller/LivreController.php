<?php

// src/Controller/ProductController.php
namespace App\Controller;

// ...
use App\Entity\Livre;
use App\Entity\Categorie;
use App\Entity\Auteur;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/Livre')]
class LivreController extends AbstractController
{
    #[Route('/create', name: 'create_Livre', methods: ['POST'])]
    public function createLivre(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $content = json_decode($request->getContent(), true);

        $Livre = new Livre();
        $Livre->setTitre($content['title']);
        $Livre->setDatePublication(new \Datetime());
        $Livre->setDisponible($content['available']);

        // tell Doctrine you want to (eventually) save the Livre (no queries yet)
        $entityManager->persist($Livre);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new Livre with id '.$Livre->getId());
    }

    #[Route('/{id}/edit', name: 'edit_Livre', methods: ['PATCH', 'PUT'])]
    public function editLivre(
        Request $request,
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        $content = json_decode($request->getContent(), true);
        $Livre = $entityManager->getRepository(Livre::class)->find($id);
        if (isset($content['title'])) {
            $Livre->setTitre($content['title']);
        }
        $Livre->setDatePublication(new \Datetime());
        $Livre->setDisponible($content['available']);

        // tell Doctrine you want to (eventually) save the Livre (no queries yet)
        $entityManager->persist($Livre);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Updated Livre with id '.$Livre->getId());
    }

    #[Route('/{id}/delete', name: 'delete_Livre', methods : ['DELETE'])]
    public function deleteLivre(
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        $Livre = $entityManager->getRepository(Livre::class)->find($id);
        if (!$Livre) {
            return new Response('Livre not found', Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($Livre);
        $entityManager->flush();

        return new Response('Deleted Livre with id '.$id);
    }

    #[Route('/{id}/get', name: 'avalaible_Livre', methods: ['GET'])]
    public function getLivre(
        EntityManagerInterface $entityManager,
        int $id
    ): Response
    {
        $Livre = $entityManager->getRepository(Livre::class)->find($id);
        if (!$Livre->isDisponible()) {
            return new Response('Livre not found', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($Livre);
    }

}