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
class getPostStatusCommand extends ContainerAwareCommand
{
//    https://www.pochta.ru/tracking?p_p_id=trackingPortlet_WAR_portalportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=getList&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_count=1&barcodeList=19010395015457&postmanAllowed=true&_=234234
    private $testEmails = array('tulupov.m@gmail.com');

    protected function configure()
    {
        $this->setName('crm:getPostStatus')
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
			SELECT u.id, u.email, u.lastName, u.firstName, u.post FROM CrmMainBundle:User u
          
            WHERE u.isProduction is NULL AND u.status != 10 AND u.enabled = 1 AND u.created < '".$date."'")->getResult();
        $txt = '';
        $t = 0;
        foreach ($orders as $order){
            $t++;
            if ($order['post']){
                $post = $order['post'];
                $json = json_decode(file_get_contents("https://www.pochta.ru/tracking?p_p_id=trackingPortlet_WAR_portalportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=getList&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_count=1&barcodeList=$post&postmanAllowed=true"));
                $output->writeln('Проверено '.$t. ' из '.count($orders));
//                var_dump("https://www.pochta.ru/tracking?p_p_id=trackingPortlet_WAR_portalportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=getList&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_count=1&barcodeList=$post&postmanAllowed=true");
                if ( $json->{'list'}[0]->{'trackingItem'}->{'hasBeenGiven'} == true ){
                    $txt .= '<a href="https://im-kard.ru/panel/operator/user/edit/'.$order['id'].'" target="_blank">'.$order['id'].' - '.$order['email']." $order[lastName] $order[firstName]".'</a><br />';
                }
                sleep(1);
            }
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Заказы скорее всего получены (Проверено на почте)')
            ->setFrom('info@im-kard.ru')
            ->setTo('imkardru@gmail.com')
            ->setBody(
                'У следующих заявок доставка прошла<br /><br />'.$txt, 'text/html'
            )
        ;
        if ($txt){
            $container->get('mailer')->send($message);
        }
    }

}

