<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserActivateCommand extends Command
{
    protected static $defaultName = 'med:user:activate';
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserRepository
     */
    private $userRepo;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserActivateCommand constructor.
     */
    public function __construct(EntityManagerInterface $em, UserRepository $userRepo, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Creation d\'un utilisateur')
            ->addArgument('email', InputArgument::REQUIRED, 'Entrer votre adresse mail')
            ->addArgument('role', InputArgument::REQUIRED, 'Entrer le role de cet utilisateur')
            ->addArgument('password', InputArgument::REQUIRED, 'Entrer votre mot de passe')
            ->setHelp(implode("\n", [
                '',
            ]))
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('email'))
        {
            $question = new Question('Merci de taper votre email:');
            $question->setValidator(function ($email){
                if (empty($email))
                {
                    throw new \Exception('Le champ mail ne peut etre vide');
                }

                /*if (!$this->userRepo->loadUserByUsername($email))
                {
                    throw new \Exception('Aucun utilisateur n\'a cet email');
                }*/

                return $email;
            });

            $questions['email'] = $question;
        }

        if (!$input->getArgument('role'))
        {
            $question = new Question('Merci de taper le role:');
            $question->setValidator(function ($role){
                if (empty($role))
                {
                    throw new \Exception('Le champ role ne peut etre vide');
                }

                /*if (!$this->userRepo->loadUserByUsername($email))
                {
                    throw new \Exception('Aucun utilisateur n\'a cet email');
                }*/

                return $role;
            });

            $questions['role'] = $question;
        }

        if (!$input->getArgument('password'))
        {
            $question = new Question('Merci de taper votre mot de passe:');
            $question->setValidator(function ($password){
                if (empty($password))
                {
                    throw new \Exception('Le champ mot de passe ne peut etre vide');
                }

                /*if (!$this->userRepo->loadUserByUsername($email))
                {
                    throw new \Exception('Aucun utilisateur n\'a cet email');
                }*/

                return $password;
            });

            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question)
        {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setEmail($email)
            ->setRoles([$role])
            ->setPassword(
                $this->passwordEncoder->encodePassword($user, $password)
            );

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('L\'utilisateur "%s" est creer avec success.', $email));
        return 0;
    }
}
