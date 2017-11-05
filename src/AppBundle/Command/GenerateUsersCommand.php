<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('users:generate:admin')
            ->setDescription('Creates starter pack for this site')
            ->setHelp('This command allows you to create an admin, an Observer and a Naturalist.');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $output->writeln(['Creation starts: ',
            'Admin '
        ]);
        $output->writeln(['You are about to create an admin user', 'Specify mail and password like this:',
            'qr@live.fr']);

        $question = new Question('Please enter user mail: ','admin@live.fr','/^[a-z0-9]{1,}@[a-z]{2,}\.[a-z]{2-4}$/i');
        $usermail = $helper->ask($input,$output,$question);


        $question2 = new Question('Please enter password, default: mon#5Pass! : ','mon#5Pass!', '/.{8,}/');//8 char min, anything
        $userpass = $helper->ask($input,$output,$question2);

        //Handle errors and password difficulty later.

        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEmail($usermail);
        $user->setUsername($usermail);
        $user->setPassword(password_hash($userpass, PASSWORD_BCRYPT));
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setEnabled(true);
        $userManager->updateUser($user);    //Create an admin user

        //Write success later



    }
}