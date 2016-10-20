<?php
namespace Panel\OperatorBundle\Command;
use
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Process\Process,
    Crm\MainBundle\Entity\StatusLog;

class SetStatusInProductionCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('crm:setstatuslog2')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $users = $em->getRepository('CrmMainBundle:CompanyUser')->findAll();

        foreach ($users as $user){
            if ($user->getDateInProductionStat() == null){
                $output->writeln($user->getId().' --- ');
                $date = $user->getCreated();
                $statusLog = new StatusLog();
                $statusLog->setCreated($date);
                $statusLog->setUpdated($date);
                $statusLog->setTitle('В&nbsp;производстве');
                $statusLog->setUser($user);
                $em->persist($statusLog);
                $em->flush($statusLog);
            }
        }
        $output->write('Конец');
    }

}

