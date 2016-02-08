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
class sendSpamCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('crm:sendSpam')
            ->setDescription('Send digest to users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTime('- 25 days');
        $date = $date->format('Y-m-d ');
        # снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
        set_time_limit(0);
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $orders = $em->createQuery("
			SELECT u.id, u.email FROM `user` u
            WHERE u.created < '$date' AND send = 0
            LIMIT 100
		")->getResult();


        foreach ($orders as $o){
            $message = \Swift_Message::newInstance()
                ->setSubject('Оставьте отзыв на сайте im-kard.ru')
                ->setFrom('info@im-kard.ru')
                ->setTo($o['email'])
                ->setBody(
                    $this->renderView(
                        'PanelOperatorBundle:Mail:setOrder.html.twig'
                    ), 'text/html'
                );
            $this->get('mailer')->send($message);
        }
    }

}

