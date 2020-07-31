<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Form\AgendaType;
use App\Repository\AgendaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\User;

/**
 * @Route("/agenda")
 */
class AgendaController extends AbstractController
{
    /**
     * @Route("/admin/rendez_vous", name="admin_agenda_index", methods={"GET"})
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(AgendaRepository $agendaRepository): Response
    {
        return $this->render('agenda/index.html.twig', [
            'agendas' => $agendaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="agenda_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $agenda = new Agenda();
        $form = $this->createForm(AgendaType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($agenda);
            $entityManager->flush();

            return $this->redirectToRoute('agenda_show', ['id' => $agenda->getId()]);
        }

        return $this->render('agenda/new.html.twig', [
            'agenda' => $agenda,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="agenda_show", methods={"GET"})
     */
    public function show(Agenda $agenda): Response
    {
        return $this->render('agenda/show.html.twig', [
            'agenda' => $agenda,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="agenda_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Agenda $agenda): Response
    {
        $form = $this->createForm(AgendaType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('agenda_show', ['id' => $agenda->getId()]);
        }

        return $this->render('agenda/edit.html.twig', [
            'agenda' => $agenda,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="agenda_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Agenda $agenda): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agenda->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($agenda);
            $entityManager->flush();
        }
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRoles('ROLE_ADMIN'))
            return $this->redirectToRoute('admin_accueil');
        else
            return $this->redirectToRoute('app_accueil');
    }
}
