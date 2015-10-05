<?php
namespace CrmAuthBundle\EventListener;

use Symfony\Component\Security\Http\Event\SwitchUserEvent;

class SwitchUserListener
{
    public function onSwitchUser(SwitchUserEvent $event)
    {
        $event->getRequest()->getSession()->set(
            '_locale',
//            $event->getTargetUser()->getLocale()
            'ru'
        );
    }
}