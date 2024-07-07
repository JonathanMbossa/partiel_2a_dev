<?php
// src/Controller/PropositionController.php

namespace App\Controller;

use App\Entity\Proposition;
use App\Entity\Election;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropositionController extends AbstractController
{
    #[Route('/proposition', name: 'create_proposition', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $election = $em->getRepository(Election::class)->find($data['election_id']);
        if (!$election) {
            return $this->json(['message' => 'Election not found'], Response::HTTP_NOT_FOUND);
        }

        $proposition = new Proposition();
        $proposition->setNom($data['nom']);
        $proposition->setDetails($data['details']);
        $proposition->setElection($election);

        $em->persist($proposition);
        $em->flush();

        return $this->json($proposition);
    }

    #[Route('/proposition', name: 'get_propositions', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $propositions = $em->getRepository(Proposition::class)->findAll();
        return $this->json($propositions);
    }

    #[Route('/proposition/{id}', name: 'get_proposition', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $proposition = $em->getRepository(Proposition::class)->find($id);
        if (!$proposition) {
            return $this->json(['message' => 'Proposition not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($proposition);
    }

    #[Route('/proposition/{id}', name: 'update_proposition', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $proposition = $em->getRepository(Proposition::class)->find($id);
        if (!$proposition) {
            return $this->json(['message' => 'Proposition not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $proposition->setNom($data['nom']);
        $proposition->setDetails($data['details']);

        $em->flush();

        return $this->json($proposition);
    }

    #[Route('/proposition/{id}', name: 'delete_proposition', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $proposition = $em->getRepository(Proposition::class)->find($id);
        if (!$proposition) {
            return $this->json(['message' => 'Proposition not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($proposition);
        $em->flush();

        return $this->json(['message' => 'Proposition deleted successfully']);
    }
}

