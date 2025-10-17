<?php

namespace App\Controller;

use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/Livre')]
class LivreController extends AbstractController
{
    #[Route('/create', name: 'create_Livre', methods: ['POST'])]
    public function createLivre(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (!$content || !isset($content['title']) || !isset($content['datePublication']) || !isset($content['available'])) {
            return new JsonResponse(['error' => 'Champs manquants (title, datePublication ,available)'], 400);
        }

        $livre = new Livre();
        $livre->setTitre($content['title']);
        $livre->setDatePublication(new \DateTime($content['datePublication']));
        $livre->setDisponible($content['available']);

        $entityManager->persist($livre);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Livre créé avec succès',
            'id' => $livre->getId(),
            'titre' => $livre->getTitre(),
            'disponible' => $livre->isDisponible(),
        ], 201);
    }

    #[Route('/{id}/edit', name: 'edit_Livre', methods: ['PATCH', 'PUT'])]
    public function editLivre(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $livre = $entityManager->getRepository(Livre::class)->find($id);

        if (!$livre) {
            return new JsonResponse(['error' => 'Livre non trouvé'], 404);
        }

        if (isset($content['title'])) {
            $livre->setTitre($content['title']);
        }
        if (isset($content['available'])) {
            $livre->setDisponible($content['available']);
        }
        if (isset($content['datePublication'])) {
            $livre->setDatePublication(new \DateTime($content['datePublication']));
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Livre mis à jour', 'id' => $livre->getId()]);
    }

    #[Route('/{id}/delete', name: 'delete_Livre', methods : ['DELETE'])]
    public function deleteLivre(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $livre = $entityManager->getRepository(Livre::class)->find($id);
        if (!$livre) {
            return new JsonResponse(['error' => 'Livre non trouvé'], 404);
        }

        $entityManager->remove($livre);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Livre supprimé', 'id' => $id]);
    }

    #[Route('/{id}/get', name: 'get_Livre', methods: ['GET'])]
    public function getLivre(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $livre = $entityManager->getRepository(Livre::class)->find($id);

        if (!$livre) {
            return new JsonResponse(['error' => 'Livre non trouvé'], 404);
        }

        return new JsonResponse([
            'id' => $livre->getId(),
            'titre' => $livre->getTitre(),
            'datePublication' => $livre->getDatePublication()->format('Y-m-d'),
            'disponible' => $livre->isDisponible(),
        ]);
    }
}
