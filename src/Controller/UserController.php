<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/signup", name="signup", methods={"POST"})
     */
    public function signup(Request $request)
    {
        $parameters = json_decode($request->getContent());

        $password = password_hash(
            $parameters->password,
            PASSWORD_BCRYPT
        );

        $user = new User();
        $user
            ->setUsername($parameters->username)
            ->setPassword($password);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json($user);
        } catch (Exception $exception) {
            // Handle Error
        }
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request) 
    {
        $parameters = json_decode($request->getContent());

        $user = $this->repository->findOneBy(
            array("username" => $parameters->username)
        );

        if (!$user) {
            return new Response(
                "Invalid username",
                Response::HTTP_NOT_FOUND
            );
        }

        $isPasswordRight = password_verify(
            $parameters->password,
            $user->getPassword()
        );

        if (!$isPasswordRight) {
            return new Response(
                "Invalid password",
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json($user);
    }
}
