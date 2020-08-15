<?php

namespace App\Controller;

use App\Entity\Element;
use App\Form\ElementType;
use App\Repository\ElementRepository;
use App\Service\ImageUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin_journal_comptable", name="admin_journal_comptable_")
 * @IsGranted("ROLE_USER")
 */
class ElementController extends AbstractController
{

    /**
     * @Route("/element", name="element", methods={"GET"})
     */
    public function index(ElementRepository $elementRepository): Response
    {
        return $this->render('admin/page_content/element/index.html.twig', [
            'elements' => $elementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="element_new", methods={"GET","POST"})
     */
    public function new(Request $request, ImageUploader $uploader): Response
    {
        $element = new Element();
        $form = $this->createForm(ElementType::class, $element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $fileTelecharger
             */
            $fileTelecharger = $form->get('file')->getData();
            if ($fileTelecharger)
            {
                $file = $uploader->upload($fileTelecharger);
                $element->setFile($file);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($element);
            $entityManager->flush();

            return $this->redirectToRoute('admin_journal_comptable_element');
        }

        return $this->render('admin/page_content/element/new.html.twig', [
            'element' => $element,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="element_show", methods={"GET"})
     */
    public function show(Element $element): Response
    {
        return $this->render('admin/page_content/element/show.html.twig', [
            'element' => $element,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="element_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Element $element): Response
    {
        $form = $this->createForm(ElementType::class, $element);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_journal_comptable_element');
        }

        return $this->render('admin/page_content/element/edit.html.twig', [
            'element' => $element,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="element_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Element $element): Response
    {
        if ($this->isCsrfTokenValid('delete'.$element->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($element);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_journal_comptable_element');
    }
}
