<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/Utilisateur')]
class UtilisateurController extends AbstractController
{
    #[Route('/create', name: 'create_Utilisateur', methods: ['POST'])]
    public function createUtilisateur(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (
            !$content ||
            !isset($content['nom']) ||
            !isset($content['prenom'])
        ) {
            return new JsonResponse(['error' => 'Champs manquants (nom, prenom)'], 400);
        }

        try {
            $utilisateur = new Utilisateur();
            $utilisateur->setNom($content['nom']);
            $utilisateur->setPrenom($content['prenom']);

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Utilisateur créé avec succès',
                'id' => $utilisateur->getId(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la création : ' . $e->getMessage()], 500);
        }
    }

    #[Route('/{id}/edit', name: 'edit_Utilisateur', methods: ['PATCH', 'PUT'])]
    public function editUtilisateur(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        if (isset($content['nom'])) {
            $utilisateur->setNom($content['nom']);
        }
        if (isset($content['prenom'])) {
            $utilisateur->setPrenom($content['prenom']);
        }

        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Utilisateur mis à jour',
            'id' => $utilisateur->getId(),
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete_Utilisateur', methods: ['DELETE'])]
    public function deleteUtilisateur(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $entityManager->remove($utilisateur);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Utilisateur supprimé',
            'id' => $id
        ]);
    }

    #[Route('/{id}/get', name: 'get_Utilisateur', methods: ['GET'])]
    public function getUtilisateur(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        return new JsonResponse([
            'id' => $utilisateur->getId(),
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
        ]);
    }
}
