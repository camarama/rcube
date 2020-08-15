<?php


namespace App\Service;

use App\Repository\DevisRepository;
use App\Repository\FacturationRepository;

class ReferenceGenerator
{
    /**
     * @var DevisRepository
     */
    private $devisRepo;
    /**
     * @var FacturationRepository
     */
    private $factureRepo;

    /**
     * ReferenceGenerator constructor.
     */
    public function __construct(DevisRepository $devisRepo, FacturationRepository $factureRepo)
    {

        $this->devisRepo = $devisRepo;
        $this->factureRepo = $factureRepo;
    }

    public function newReference()
    {
        $reference = $this->devisRepo->findOneBy(['valider' => 1], ['id' => 'DESC'], 1, 1);

//        dd($reference);
        if (!$reference)
            return 1;
        else
            return $reference->getReference() + 1;

    }

    public function newRefFacture()
    {
        $refFacture = $this->factureRepo->findOneBy(['valider' => 1], ['id' => 'DESC'], 1, 1);

        if (!$refFacture)
            return 1;
        else
            return $refFacture->getReference() + 1;
    }
}