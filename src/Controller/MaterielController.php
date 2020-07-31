<?php

namespace App\Controller;

use App\Entity\Materiel;
use App\Form\MaterielType;
use App\Repository\MaterielRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/materiel", name="admin_")
 * @IsGranted("ROLE_USER")
 */
class MaterielController extends AbstractController
{
    /**
     * @Route("/", name="materiel_index", methods={"GET"})
     */
    public function index(MaterielRepository $materielRepository): Response
    {
        return $this->render('admin/page_content/materiel/index.html.twig', [
            'materiels' => $materielRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="materiel_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $materiel = new Materiel();
        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($materiel);
            $entityManager->flush();

            return $this->redirectToRoute('admin_materiel_index');
        }

        return $this->render('admin/page_content/materiel/new.html.twig', [
            'materiel' => $materiel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="materiel_show", methods={"GET"})
     */
    public function show(Materiel $materiel): Response
    {
        return $this->render('admin/page_content/materiel/show.html.twig', [
            'materiel' => $materiel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="materiel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Materiel $materiel): Response
    {
        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_materiel_index');
        }

        return $this->render('admin/page_content/materiel/edit.html.twig', [
            'materiel' => $materiel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="materiel_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Materiel $materiel): Response
    {
        if ($this->isCsrfTokenValid('delete'.$materiel->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($materiel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_materiel_index');
    }
}
