<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Iphp\FileStoreBundle\IphpFileStoreBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new TFox\MpdfPortBundle\TFoxMpdfPortBundle(),

//            new JMS\Payment\CoreBundle\JMSPaymentCoreBundle(),
//            new JMS\DiExtraBundle\JMSDiExtraBundle(),
//            new JMS\AopBundle\JMSAopBundle(),
//            new Rispo\YandexKassaBundle\RispoYandexKassaBundle(),

            new Crm\MainBundle\CrmMainBundle(),
            new Crm\AdminBundle\CrmAdminBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Crm\OperatorBundle\CrmOperatorBundle(),
            new Crm\FaceBundle\CrmFaceBundle(),
            new Crm\ImageBundle\CrmImageBundle(),
            new Panel\OperatorBundle\PanelOperatorBundle(),
            new Craue\FormFlowBundle\CraueFormFlowBundle(),
            new Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle(),
            new Crm\AuthBundle\CrmAuthBundle(),

        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
