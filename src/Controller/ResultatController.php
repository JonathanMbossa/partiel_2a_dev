<?php

// src/Controller/ResultatController.php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\Bulletin;
use App\Entity\Proposition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultatController extends AbstractController
{
    #[Route('/election/{id}/result', name: 'get_result')]
    public function getResult(int $id, EntityManagerInterface $em): Response
    {
        $election = $em->getRepository(Election::class)->find($id);
        if (!$election) {
            throw $this->createNotFoundException('Election not found');
        }

        // Get all bulletins for the election
        $bulletins = $em->getRepository(Bulletin::class)->findBy(['election' => $election]);

        // Count votes for each proposition
        $votes = [];
        foreach ($bulletins as $bulletin) {
            foreach ($bulletin->getChoix() as $choix) {
                if (!isset($votes[$choix])) {
                    $votes[$choix] = 0;
                }
                $votes[$choix]++;
            }
        }

        // Find the proposition with the highest vote
        $winningPropositionId = array_search(max($votes), $votes);
        $winningProposition = $em->getRepository(Proposition::class)->find($winningPropositionId);

        return $this->render('resultat/view.html.twig', [
            'election' => $election,
            'winning_proposition' => $winningProposition,
            'votes' => $votes,
        ]);
    }
}

