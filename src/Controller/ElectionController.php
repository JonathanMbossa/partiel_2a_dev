<?php
// src/Controller/ElectionController.php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\Proposition;
use App\Entity\Bulletin;
use App\Form\BulletinFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ElectionController extends AbstractController
{
    #[Route('/election', name: 'election_vote')]
    public function vote(Request $request, EntityManagerInterface $em): Response
    {
        // Création d'une élection et des propositions pour l'exemple
        $election = new Election();
        $election->setSujet('Quelle couleur préférez-vous ?');
        $election->setDate(new \DateTime());
        $election->setQuota(3); // Exemple de quota

        $propositionsNoms = ['Bleu', 'Vert', 'Violet', 'Marron', 'Jaune', 'Rose'];
        foreach ($propositionsNoms as $propositionNom) {
            $proposition = new Proposition();
            $proposition->setNom($propositionNom);
            $proposition->setDetails('');
            $election->addProposition($proposition);
        }

        $form = $this->createForm(BulletinFormType::class, null, options: [
            'propositions' => array_reduce($election->getPropositions()->toArray(), function ($result, $item) {
                $result[$item->getNom()] = $item->getId();
                return $result;
            }, [])
        ]);

        $form->handleRequest($request);
        $results = [];
        $submitted = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $submitted = true;
            $data = $form->getData();
            $bulletin = new Bulletin();
            $bulletin->setChoix($data['choix']);
            $bulletin->setElection($election);


            // Compter les votes
            foreach ($election->getPropositions() as $proposition) {
                $results[$proposition->getNom()] = in_array($proposition->getId(), $data['choix']) ? 1 : 0;
            }
        }

        return $this->render('election/vote.html.twig', [
            'election' => $election,
            'form' => $form->createView(),
            'submitted' => $submitted,
            'results' => $results,
        ]);
    }
}
