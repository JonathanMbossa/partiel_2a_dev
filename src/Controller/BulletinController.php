<?php

// src/Controller/BulletinController.php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\Proposition;
use App\Entity\Bulletin;
use App\Form\BulletinType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BulletinController extends AbstractController
{
    #[Route('/election/{id}/vote', name: 'vote')]
    public function vote(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $election = $em->getRepository(Election::class)->find($id);
        if (!$election) {
            throw $this->createNotFoundException('Election not found');
        }

        $propositions = $em->getRepository(Proposition::class)->findBy(['election' => $election]);

        $form = $this->createForm(BulletinType::class, null, [
            'propositions' => array_reduce($propositions, function ($result, $item) {
                $result[$item->getNom()] = $item->getId();
                return $result;
            }, [])
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $bulletin = new Bulletin();
            $bulletin->setChoix($data['choix']);
            $bulletin->setElection($election);

            $em->persist($bulletin);
            $em->flush();

            // Rediriger vers la page de résultat ou confirmation
            return $this->redirectToRoute('get_result', ['id' => $election->getId()]);
        }

        return $this->render('bulletin/vote.html.twig', [
            'election' => $election,
            'propositions' => $propositions,
            'form' => $form->createView(),
        ]);
    }
}
