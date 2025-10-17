<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Repository\EmpruntRepository;
use App\Repository\LivreRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/emprunt', name: 'app_emprunt_')]
class EmpruntController extends AbstractController
{
    #[Route('/demander/{livreId}/{utilisateurId}', name: 'demander', methods: ['POST'])]
    public function demander(
        int $livreId,
        int $utilisateurId,
        LivreRepository $livreRepo,
        UtilisateurRepository $utilisateurRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $livre = $livreRepo->find($livreId);
        $utilisateur = $utilisateurRepo->find($utilisateurId);

        if (!$livre) {
            return new JsonResponse(['error' => 'Livre introuvable'], 404);
        }

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'Utilisateur introuvable'], 404);
        }

        if (!$livre->isDisponible()) {
            return new JsonResponse(['error' => 'Ce livre est déjà emprunté'], 400);
        }

        $emprunt = new Emprunt();
        $emprunt->setIdLivre($livre);
        $emprunt->setIdUtilisateur($utilisateur);
        $emprunt->setDateEmprunt(new \DateTime());

        // Le livre devient indisponible
        $livre->setDisponible(false);

        $em->persist($emprunt);
        $em->flush();

        return new JsonResponse([
            'message' => 'Livre emprunté avec succès',
            'livre' => $livre->getTitre(),
            'utilisateur' => $utilisateur->getPrenom() . ' ' . $utilisateur->getNom(),
            'dateEmprunt' => $emprunt->getDateEmprunt()->format('Y-m-d H:i')
        ]);
    }

    #[Route('/rendre/{id}', name: 'rendre', methods: ['POST'])]
    public function rendre(
        int $id,
        EmpruntRepository $empruntRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $emprunt = $empruntRepo->find($id);

        if (!$emprunt) {
            return new JsonResponse(['error' => 'Emprunt introuvable'], 404);
        }

        if ($emprunt->getDateRendus() !== null) {
            return new JsonResponse(['error' => 'Ce livre a déjà été rendu'], 400);
        }

        $emprunt->setDateRendus(new \DateTime());
        $livre = $emprunt->getIdLivre();
        $livre->setDisponible(true);

        $em->flush();

        return new JsonResponse([
            'message' => 'Livre rendu avec succès',
            'livre' => $livre->getTitre(),
            'dateRendus' => $emprunt->getDateRendus()->format('Y-m-d H:i')
        ]);
    }

    #[Route('/utilisateur/{id}', name: 'utilisateur_emprunts', methods: ['GET'])]
    public function empruntsUtilisateur(
        int $id,
        EmpruntRepository $empruntRepo
    ): JsonResponse {
        $emprunts = $empruntRepo->findBy(
            ['idUtilisateur' => $id, 'DateRendus' => null],
            ['DateEmprunt' => 'ASC']
        );

        $data = array_map(function (Emprunt $e) {
            return [
                'idEmprunt' => $e->getId(),
                'livre' => $e->getIdLivre()->getTitre(),
                'dateEmprunt' => $e->getDateEmprunt()->format('Y-m-d'),
            ];
        }, $emprunts);

        return new JsonResponse([
            'utilisateur_id' => $id,
            'emprunts_en_cours' => $data,
            'total' => count($data)
        ]);
    }

    #[Route('/periode/{auteurId}/{debut}/{fin}', name: 'entre_dates', methods: ['GET'])]
    public function empruntsEntreDates(
        int $auteurId,
        string $debut,
        string $fin,
        EmpruntRepository $empruntRepo,
        AuteurRepository $auteurRepo
    ): JsonResponse {
        // Vérifier que l'auteur existe
        $auteur = $auteurRepo->find($auteurId);
        if (!$auteur) {
            return new JsonResponse(['error' => 'Auteur introuvable'], 404);
        }
        try {
            $dateDebut = new \DateTime($debut);
            $dateFin = new \DateTime($fin);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Format de date invalide. Utilisez YYYY-MM-DD'], 400);
        }

        if ($dateDebut > $dateFin) {
            return new JsonResponse(['error' => 'La date de début doit être inférieure ou égale à la date de fin'], 400);
        }

    $emprunts = $empruntRepo->findEmpruntsBetweenDatesByAuthor($dateDebut, $dateFin, $auteurId);

        $data = array_map(function (Emprunt $e) {
            return [
                'livre' => $e->getIdLivre()->getTitre(),
                'dateEmprunt' => $e->getDateEmprunt()->format('Y-m-d'),
                'utilisateur' => $e->getIdUtilisateur()->getPrenom() . ' ' . $e->getIdUtilisateur()->getNom(),
            ];
        }, $emprunts);

        return new JsonResponse([
            'auteur' => $auteur->getPrenom() . ' ' . $auteur->getNom(),
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d'),
            ],
            'emprunts' => $data,
        ]);
    }
}