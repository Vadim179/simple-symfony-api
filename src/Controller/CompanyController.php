<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @Route("/companies", name="companies")
 */
class CompanyController extends AbstractController
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, CompanyRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="get-companies", methods={"GET"})
     */
    public function index()
    {
        $companies = $this->repository->findAll();
        return $this->json($companies);
    }

    /**
     * @Route("/{id}", name="get-company", methods={"GET"})
     */
    public function getById(int $id)
    {
        $company = $this->repository->find($id);
        return $this->json($company);
    }

    /**
     * @Route(name="create-company", methods={"POST"})
     */
    public function create(Request $request)
    {
        $parameters = json_decode($request->getContent());

        $company = new Company();
        $company->setName($parameters->name);

        try {
            $this->entityManager->persist($company);
            $this->entityManager->flush();
            return $this->json($company);
        } catch (Exception $exception) {
            // Handle Error 
        }
    }

    /**
     * @Route("/{id}", name="edit-company", methods={"PUT"})
     */
    public function edit(Request $request, int $id)
    {
        $parameters = json_decode($request->getContent());

        $company = $this->repository->find($id);
        $company->setName($parameters->name);

        $this->entityManager->flush();

        return $this->json($company);
    }

    /**
     * @Route("/{id}", name="delete-company", methods={"DELETE"})
     */
    public function remove(int $id)
    {
        $company = $this->repository->find($id);

        try {
            $this->entityManager->remove($company);
            $this->entityManager->flush();
            return new Response(
                "Removed company successfully",
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            // Handle Error
        }
    }
}