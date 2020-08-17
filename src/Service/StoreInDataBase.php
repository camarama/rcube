<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Devis;
use App\Repository\ClientRepository;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class StoreInDataBase
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ClientRepository
     */
    private $clientRepo;
    /**
     * @var DevisRepository
     */
    private $devisRepo;

    /**
     * StoreInDataBase constructor.
     */
    public function __construct(SessionInterface $session, EntityManagerInterface $manager, ClientRepository $clientRepo, DevisRepository $devisRepo)
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->clientRepo = $clientRepo;
        $this->devisRepo = $devisRepo;
    }

    public function prepareCommande()
    {
        if (!$this->session->has('prestation'))
            return $this->redirectToRoute('admin_presentation');

        $prestation = $this->session->get('prestation');
        $client = $this->clientRepo->find($prestation['client']);

        if (!$this->session->has('devis'))
            $devis = new Devis();
        else
            $devis = $this->devisRepo->find($this->session->get('devis'));

        $devis->setClient($client);
        $devis->setDate(new \DateTime());
        $devis->setValider(0);
        $devis->setReference(0);
        $devis->setPrestation($prestation['devis']);

        if (!$this->session->has('devis'))
        {
            $this->manager->persist($devis);
            $this->session->set('devis', $devis);
        }

        $this->manager->flush();

        return new Response($devis->getId());
    }
}