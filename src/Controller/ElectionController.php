<?php


namespace App\Controller;

use App\Entity\Election;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ElectionController extends AbstractController
{
    #[Route('/election', name: 'create_election', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $election = new Election();
        $election->setSujet($data['sujet']);
        $election->setDate(new \DateTime($data['date']));
        $election->setQuota($data['quota']);

        $em->persist($election);
        $em->flush();

        return $this->json($election);
    }

    #[Route('/election', name: 'get_elections', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $elections = $em->getRepository(Election::class)->findAll();
        return $this->json($elections);
    }

    #[Route('/election/{id}', name: 'get_election', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $election = $em->getRepository(Election::class)->find($id);
        if (!$election) {
            return $this->json(['message' => 'Election not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($election);
    }

    #[Route('/election/{id}', name: 'update_election', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $election = $em->getRepository(Election::class)->find($id);
        if (!$election) {
            return $this->json(['message' => 'Election not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $election->setSujet($data['sujet']);
        $election->setDate(new \DateTime($data['date']));
        $election->setQuota($data['quota']);

        $em->flush();

        return $this->json($election);
    }

    #[Route('/election/{id}', name: 'delete_election', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $election = $em->getRepository(Election::class)->find($id);
        if (!$election) {
            return $this->json(['message' => 'Election not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($election);
        $em->flush();

        return $this->json(['message' => 'Election deleted successfully']);
    }
}

