<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserModifyCommand extends Command
{
    protected static $defaultName = 'med:user:modify';
    /**
     * @var UserRepository
     */
    private $userRepo;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * UserModifyEmailCommand constructor.
     */
    public function __construct(UserRepository $userRepo, EntityManagerInterface $em, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userRepo = $userRepo;
        $this->em = $em;
        $this->userPasswordEncoder = $userPasswordEncoder;

        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Modification de l\'utilisateur')
            ->addArgument('email', InputArgument::REQUIRED, 'Entrer l\'email')
//            ->addArgument('email', InputArgument::REQUIRED, 'Entrer le nouveau role')
            ->addArgument('password', InputArgument::REQUIRED, 'Entrer le nouveau mot de passe')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
            ->setHelp(implode("\n", [
                '',
            ]))
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];

        if (!$input->getArgument('email'))
        {
            $question = new Question('Taper l\'adresse mail de l\'utilisateur a modifier:');
            $question->setValidator(function ($email){
                if (empty($email))
                {
                    throw new \Exception('Le champ mail ne peut etre vide');
                }

                if (!$this->userRepo->loadUserByUsername($email))
                {
                    throw new \Exception('Aucun utilisateur n\'a cet email');
                }

                return $email;
            });

            $questions['email'] = $question;
        }

        if (!$input->getArgument('password'))
        {
            $question = new Question('Taper votre nouveau mot de passe:');
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
        $user = $this->userRepo->findOneBy(['email' => $email]);

        $user->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $user,
                $input->getArgument('password')
            )
        );

        $this->em->flush();

        $io->success('Les modifications sont validées avec success');

        return 0;
    }
}
