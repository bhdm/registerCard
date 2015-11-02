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
class StatusCommand extends ContainerAwareCommand
{
    private $testEmails = array('tulupov.m@gmail.com', 'plisenkova.o@evrika.ru');

    protected function configure()
    {
        $this->setName('crm:status')
            ->setDescription('Send digest to users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTime('- 20 days');
        $date = $date->format('Y-m-d');
        # снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
        set_time_limit(0);
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $orders = $em->createQuery("
			SELECT u.id, u.email FROM `StatusLog` sl
            LEFT JOIN user  u ON u.id = sl.user_id
            WHERE sl.id = (SELECT MAX(id) FROM StatusLog sl2 WHERE sl2.user_id = u.id ) AND sl.title = 'На почте' AND sl.created >= '".$date." 00:00:00' AND sl.created <= '".$date." 23:59:59'
		")->getResult();

        $txt = '';
        foreach ($orders as $order){
//            $txt = '<a href="https://im-kard.ru/panel/operator/user/edit/'.$order['id'].'" target="_blank">'.$order['id'].' - '.$order['email'].'</a><br />';
              $output->writeln('<a href="https://im-kard.ru/panel/operator/user/edit/'.$order['id'].'" target="_blank">'.$order['id'].' - '.$order['email'].'</a><br />');
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Заказы скорее всего получены')
            ->setFrom('info@im-kard.ru')
            ->setTo('imkardru@gmail.com')
            ->setBody(
                $this->renderView(
                    'PanelOperatorBundle:Mail:statusCommand.html.twig',
                    array('txt' => $txt)
                ), 'text/html'
            )
        ;
        $this->get('mailer')->send($message);
    }

}

