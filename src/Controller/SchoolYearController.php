<?php

namespace App\Controller;

use App\Entity\SchoolYear;
use App\Form\SchoolYearType;
use App\Repository\SchoolYearRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/school-year")
 */
class SchoolYearController extends AbstractController
{
    /**
     * @Route("/", name="school_year_index", methods={"GET"})
     */
    public function index(SchoolYearRepository $schoolYearRepository, StudentRepository $studentRepository): Response
    {
        // Par défaut, les utilisateurs voient toutes les school years.
        // Mais les students ne sont pas censés voir les autres school
        // years que la leur.
        $schoolYears = $schoolYearRepository->findAll();

        // On récupère le compte de l'utilisateur authentifié
        $user = $this->getUser();

        // On vérifie si l'utilisateur est un student
        // Note : on peut aussi utiliser $this->isGranted('ROLE_STUDENT') au
        // lieu de in_array('ROLE_STUDENT', $user->getRoles()).
        if (in_array('ROLE_STUDENT', $user->getRoles())) {
            // L'utilisateur est un student

            // On récupère le profil student lié au compte utilisateur
            $student = $studentRepository->findOneByUser($user);

            // On récupère la school year de l'utilisater 
            $schoolYear = $student->getSchoolYear();
            // On créé un tableau avec la school year de l'utilisateur.
            // On est obligé de créer un tableau dans la variable $schoolYears
            // car le template s'attend à ce qu'il puisse boucler sur la
            // variable school_years.
            $schoolYears = [$schoolYear];
        }

        return $this->render('school_year/index.html.twig', [
            'school_years' => $schoolYears,
        ]);
    }

    /**
     * @Route("/new", name="school_year_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $schoolYear = new SchoolYear();
        $form = $this->createForm(SchoolYearType::class, $schoolYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($schoolYear);
            $entityManager->flush();

            return $this->redirectToRoute('school_year_show', [
                'id' => $schoolYear->getId(),
            ]);
        }

        return $this->render('school_year/new.html.twig', [
            'school_year' => $schoolYear,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="school_year_show", methods={"GET"})
     */
    public function show(SchoolYear $schoolYear): Response
    {
        return $this->render('school_year/show.html.twig', [
            'school_year' => $schoolYear,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="school_year_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SchoolYear $schoolYear): Response
    {
        $form = $this->createForm(SchoolYearType::class, $schoolYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('school_year_show', [
                'id' => $schoolYear->getId(),
            ]);
        }

        return $this->render('school_year/edit.html.twig', [
            'school_year' => $schoolYear,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="school_year_delete", methods={"POST"})
     */
    public function delete(Request $request, SchoolYear $schoolYear): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolYear->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($schoolYear);
            $entityManager->flush();
        }

        return $this->redirectToRoute('school_year_index');
    }
}
