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
//    https://www.pochta.ru/tracking?p_p_id=trackingPortlet_WAR_portalportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=getList&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_count=1&barcodeList=19010395015457&postmanAllowed=true&_=234234
    private $testEmails = array('tulupov.m@gmail.com');

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
			SELECT u.id, u.email FROM CrmMainBundle:StatusLog sl
            LEFT JOIN sl.user u 
            WHERE sl.id = 
            (SELECT MAX(sl2.id) FROM CrmMainBundle:StatusLog sl2 WHERE sl2.user = u ) AND sl.title = 'На почте' AND sl.created >= '".$date." 00:00:00' AND sl.created <= '".$date." 23:59:59'
		")->getResult();

        $txt = '';
        foreach ($orders as $order){
            $txt = '<a href="https://im-kard.ru/panel/operator/user/edit/'.$order['id'].'" target="_blank">'.$order['id'].' - '.$order['email'].'</a><br />';
              $output->writeln('<a href="https://im-kard.ru/panel/operator/user/edit/'.$order['id'].'" target="_blank">'.$order['id'].' - '.$order['email'].'</a><br />');
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Заказы скорее всего получены')
            ->setFrom('info@im-kard.ru')
            ->setTo('imkardru@gmail.com')
            ->setBody(
                'У следующих заявок доставка длиться уже 20 дней<br /><br />'.$txt, 'text/html'
            )
        ;
        $container->get('mailer')->send($message);
    }

}

