<?php
namespace Panel\OperatorBundle\Command;
use
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Process\Process,
    Crm\MainBundle\Entity\GeoStatistic;

class GeoStatisticCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('crm:geostatistic');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
        set_time_limit(0);
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

        $users = $em->getRepository('CrmMainBundle:User')->findAll();

        foreach ($users as $key => $user){
            if ( $user->getDileveryRegion() != null){
                $region = $user->getDileveryRegion();
            }else{
                if (isset($user->getDeliveryAdrs()['region'])){
                    $region = $user->getDeliveryAdrs()['region'];
                }else{
                    $region = null;
                }
            }

            if ( $user->getDileveryCity() != null){
                $city = $user->getDileveryCity();
            }else{
                if (isset($user->getDeliveryAdrs()['city'])){
                    $city = $user->getDeliveryAdrs()['city'];
                }else{
                    $city = null;
                }
            }

            $stat = $em->getRepository('CrmMainBundle:GeoStatistic')->findOneBy(['region' => $region, 'city' => $city]);
            if ($region != null || $city != null){
                if ($stat == null){
                    $stat = new GeoStatistic();
                    $stat->setRegion($region);
                    $stat->setCity($city);
                    $stat->setCount(1);
                    $em->persist($stat);
                }else{
                    $stat->setCount(($stat->getCount()+1));
                }
                $em->flush();
                $output->writeln($key);
            }
        }
    }

}

