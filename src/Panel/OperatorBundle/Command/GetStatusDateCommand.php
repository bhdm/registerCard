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
class GetStatusDateCommand extends ContainerAwareCommand
{
    private $testEmails = array('tulupov.m@gmail.com', 'plisenkova.o@evrika.ru');

    protected function configure()
    {
        $this->setName('crm:getstatusdate')
            ->setDescription('Добавляет дату оплаты в пользователя');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $users = $em->getRepository('CrmMainBundle:User')->filterByCommand();

        foreach ($users as $user){
            $output->write('Пользователь с ID '.$user->getId());
            $date = $user->getDateInProduction();
            $output->write(' '.($date ? '0' : $date->format('d.m.Y')));
            if ($date !== null){
                $user->setIsProduction($date);
                $em->flush($user);
                $output->writeln(' '.$date->format('d.m.Y'));
            }else{
                $output->writeln(' --- ');
            }
        }
        $output->write('Карты предприятий и мастерских');

        $users = $em->getRepository('CrmMainBundle:CompanyUser')->findBy(['isProduction' => null],['id' => 'ASC']);

        foreach ($users as $user){
            $output->write('Пользователь с ID '.$user->getId());
            $date = new \DateTime();
            if ($user->getStatus() > 0 && $user->getStatus() != 10 ){
                $user->setIsProduction($date);
                $em->flush($user);
                $output->writeln(' '.$date->format('d.m.Y'));
            }else{
                $output->writeln(' --- ');
            }
        }
        $output->write('Конец');
    }

}

