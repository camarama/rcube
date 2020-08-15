<?php

namespace App\Controller;

use App\Entity\Rubrique;
use App\Form\RubriqueType;
use App\Repository\RubriqueRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/journal_compable", name="admin_journal_comptable_")
 * @IsGranted("ROLE_USER")
 */
class RubriqueController extends AbstractController
{
    /**
     * @Route("/rubrique", name="rubrique", methods={"GET"})
     */
    public function index(RubriqueRepository $rubriqueRepository): Response
    {
        return $this->render('admin/page_content/rubrique/index.html.twig', [
            'rubriques' => $rubriqueRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="rubrique_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $rubrique = new Rubrique();
        $form = $this->createForm(RubriqueType::class, $rubrique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rubrique);
            $entityManager->flush();

            return $this->redirectToRoute('admin_journal_comptable_rubrique');
        }

        return $this->render('admin/page_content/rubrique/new.html.twig', [
            'rubrique' => $rubrique,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rubrique_show", methods={"GET"})
     */
    public function show(Rubrique $rubrique): Response
    {
        return $this->render('admin/page_content/rubrique/show.html.twig', [
            'rubrique' => $rubrique,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="rubrique_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Rubrique $rubrique): Response
    {
        $form = $this->createForm(RubriqueType::class, $rubrique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_journal_comptable_rubrique');
        }

        return $this->render('admin/page_content/rubrique/edit.html.twig', [
            'rubrique' => $rubrique,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="rubrique_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Rubrique $rubrique): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rubrique->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($rubrique);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_journal_comptable_rubrique');
    }
}
