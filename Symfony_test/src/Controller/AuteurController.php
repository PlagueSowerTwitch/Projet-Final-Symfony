<?php

namespace App\Controller;

use App\Entity\Auteur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/Auteur')]
class AuteurController extends AbstractController
{
    #[Route('/create', name: 'create_Auteur', methods: ['POST'])]
    public function createAuteur(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (
            !$content ||
            !isset($content['nom']) ||
            !isset($content['prenom']) ||
            !isset($content['biographie']) ||
            !isset($content['dateNaissance'])
        ) {
            return new JsonResponse(['error' => 'Champs manquants (nom, prenom, biographie, dateNaissance)'], 400);
        }

        try {
            $auteur = new Auteur();
            $auteur->setNom($content['nom']);
            $auteur->setPrenom($content['prenom']);
            $auteur->setBiographie($content['biographie']);
            $auteur->setDateNaissance(new \DateTime($content['dateNaissance']));

            $entityManager->persist($auteur);
            $entityManager->flush();

            return new JsonResponse([
                'message' => 'Auteur créé avec succès',
                'id' => $auteur->getId(),
                'nom' => $auteur->getNom(),
                'prenom' => $auteur->getPrenom(),
                'biographie' => $auteur->getBiographie(),
                'dateNaissance' => $auteur->getDateNaissance()->format('Y-m-d'),
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la création : ' . $e->getMessage()], 500);
        }
    }

    #[Route('/{id}/edit', name: 'edit_Auteur', methods: ['PATCH', 'PUT'])]
    public function editAuteur(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $auteur = $entityManager->getRepository(Auteur::class)->find($id);

        if (!$auteur) {
            return new JsonResponse(['error' => 'Auteur non trouvé'], 404);
        }

        if (isset($content['nom'])) {
            $auteur->setNom($content['nom']);
        }
        if (isset($content['prenom'])) {
            $auteur->setPrenom($content['prenom']);
        }
        if (isset($content['biographie'])) {
            $auteur->setBiographie($content['biographie']);
        }
        if (isset($content['dateNaissance'])) {
            $auteur->setDateNaissance(new \DateTime($content['dateNaissance']));
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Auteur mis à jour', 'id' => $auteur->getId()]);
    }

    #[Route('/{id}/delete', name: 'delete_Auteur', methods: ['DELETE'])]
    public function deleteAuteur(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $auteur = $entityManager->getRepository(Auteur::class)->find($id);

        if (!$auteur) {
            return new JsonResponse(['error' => 'Auteur non trouvé'], 404);
        }

        $entityManager->remove($auteur);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Auteur supprimé', 'id' => $id]);
    }

    #[Route('/{id}/get', name: 'get_Auteur', methods: ['GET'])]
    public function getAuteur(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $auteur = $entityManager->getRepository(Auteur::class)->find($id);

        if (!$auteur) {
            return new JsonResponse(['error' => 'Auteur non trouvé'], 404);
        }

        return new JsonResponse([
            'id' => $auteur->getId(),
            'nom' => $auteur->getNom(),
            'prenom' => $auteur->getPrenom(),
            'biographie' => $auteur->getBiographie(),
            'dateNaissance' => $auteur->getDateNaissance()->format('Y-m-d'),
        ]);
    }
}
