<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/Categorie')]
class CategorieController extends AbstractController
{
    #[Route('/create', name: 'create_Categorie', methods: ['POST'])]
    public function createCategorie(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (!$content || !isset($content['nom']) || !isset($content['description'])) {
            return new JsonResponse(['error' => 'Champs manquants (nom, description)'], 400);
        }

        $categorie = new Categorie();
        $categorie->setNom($content['nom']);
        $categorie->setDescription($content['description']);

        $entityManager->persist($categorie);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Categorie créée avec succès',
            'id' => $categorie->getId(),
            'nom' => $categorie->getNom(),
        ], 201);
    }

    #[Route('/{id}/edit', name: 'edit_Categorie', methods: ['PATCH', 'PUT'])]
    public function editCategorie(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            return new JsonResponse(['error' => 'Categorie non trouvée'], 404);
        }

        if (isset($content['nom'])) {
            $categorie->setNom($content['nom']);
        }
        if (isset($content['description'])) {
            $categorie->setDescription($content['description']);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Categorie mise à jour', 'id' => $categorie->getId()]);
    }

    #[Route('/{id}/delete', name: 'delete_Categorie', methods: ['DELETE'])]
    public function deleteCategorie(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        if (!$categorie) {
            return new JsonResponse(['error' => 'Categorie non trouvée'], 404);
        }

        $entityManager->remove($categorie);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Categorie supprimée', 'id' => $id]);
    }

    #[Route('/{id}/get', name: 'get_Categorie', methods: ['GET'])]
    public function getCategorie(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            return new JsonResponse(['error' => 'Categorie non trouvée'], 404);
        }

        return new JsonResponse([
            'id' => $categorie->getId(),
            'nom' => $categorie->getNom(),
            'description' => $categorie->getDescription(),
        ]);
    }
}
