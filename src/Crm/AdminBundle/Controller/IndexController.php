<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/admin", name="admin_main")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if ($request->getMethod()=='POST'){
            $user = 'adminuser';
            $password = 'adinuser4';

            if ($request->request->get('asuser')==$user && $request->request->get('aspassword')==$password){
                $hash = md5($user.$password);
                $request->getSession()->set('hash',$hash);
                return $this->redirect($this->generateUrl('user_list'));
            }
        }
        return array();
    }
}
