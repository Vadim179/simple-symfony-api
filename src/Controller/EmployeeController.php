<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Company;
use App\Entity\Role;
use App\Repository\EmployeeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @Route("/employees", name="employees")
 */
class EmployeeController extends AbstractController
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, EmployeeRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="get-employees", methods={"GET"})
     */
    public function index()
    {
        $employees = $this->repository->findAll();
        return $this->json($employees);
    }

    /**
     * @Route("/{id}", name="get-employee", methods={"GET"})
     */
    public function getById(int $id) 
    {
        $employee = $this->repository->find($id);
        return $this->json($employee->toArray());
    }

    /**
     * @Route(name="create-employee", methods={"POST"})
     */
    public function create(Request $request) {
        $parameters = json_decode($request->getContent());

        $employee = new Employee();
        $employee->setName($parameters->name);

        $company = $this
            ->getDoctrine()
            ->getRepository(Company::class)
            ->find($parameters->companyId);

        if ($company) {
            $employee->setCompany($company);
        }

        $roles = $this
            ->getDoctrine()
            ->getRepository(Role::class)
            ->findAll();

        foreach ($roles as $role) {
            if (in_array($role->getId(), $parameters->roleIds)) {
                $employee->addRole($role);
            }
        }

        try {
            $this->entityManager->persist($employee);
            $this->entityManager->flush();
            return $this->json($employee);
        } catch(Exception $exception) {
            // Handle Error
        }
    }

    /**
     * @Route("/{id}", name="update-employee", methods={"PUT"})
     */
    public function update(Request $request, int $id) {
        $parameters = json_decode($request->getContent());
        
        $employee = $this->repository->find($id);
        $employee->setName($parameters->name);

        $company = $this
            ->getDoctrine()
            ->getRepository(Company::class)
            ->find($parameters->companyId);

        if ($company) {
            $employee->setCompany($company);
        }
        
        $roles = $this
            ->getDoctrine()
            ->getRepository(Role::class)
            ->findAll();

        foreach ($employee->getRoles() as $role) {
            if (!in_array($role->getId(), $parameters->roleIds)) {
                $employee->removeRole($role);
            }
        }
        foreach ($roles as $role) {
            if (in_array($role->getId(), $parameters->roleIds)) {
                $employee->addRole($role);
            }
        }

        $this->entityManager->flush();

        return $this->json($employee);
    }

    /**
     * @Route("/{id}", name="delete-employee", methods={"DELETE"})
     */
    public function remove(int $id) 
    {
        $employee = $this->repository->find($id);

        try {
            $this->entityManager->remove($employee);
            $this->entityManager->flush();
            return new Response(
                "Removed employee successfully",
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            // Handle Error 
        }
    }
}
