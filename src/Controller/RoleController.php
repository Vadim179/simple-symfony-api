<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @Route("/roles", name="roles")
 */
class RoleController extends AbstractController
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, RoleRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="get-roles", methods={"GET"})
     */
    public function index()
    {
        $roles = $this->repository->findAll();
        return $this->json($roles);
    }

    /**
     * @Route("/{id}", name="get-role", methods={"GET"})
     */ 
    public function getById(int $id)
    {
        $role = $this->repository->find($id);
        return $this->json($role);
    }

    /**
     * @Route(name="create-role", methods={"POST"})
     */
    public function create(Request $request)
    {
        $parameters = json_decode($request->getContent());

        $role = new Role();
        $role->setName($parameters->name);

        try {
            $this->entityManager->persist($role);
            $this->entityManager->flush();
            return $this->json($role);
        } catch(Exception $exception) {
            // Error Handler
        }
    }

    /**
     * @Route("/{id}", name="edit-role", methods={"PUT"})
     */
    public function edit(Request $request, int $id)
    {
        $parameters = json_decode($request->getContent());
    
        $role = $this->repository->find($id);
        $role->setName($parameters->name);
        
        $this->entityManager->flush();

        return $this->json($role);
    }

    /**
     * @Route("/{id}", name="delete-role", methods={"DELETE"})
     */
    public function remove(int $id) 
    {
        $role = $this->repository->find($id);

        try {
            $this->entityManager->remove($role);
            $this->entityManager->flush();
            return new Response(
                "Removed role successfully",
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            // Handle Error
        }
    }
}