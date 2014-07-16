<?php

namespace Crm\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Crm\MainBundle\Entity\Company;

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
            }else{
                $company = $this->getDoctrine()->getRepository('CrmMainBundle:Company')->findOneByLogin($request->request->get('asuser'));
                if ($company){
                    if ( $company->getPassword() == $request->request->get('aspassword')){
                        if ($company->getEnabled() == 1){
                            $request->getSession()->set('role','ROLE_COMPANY');
                            $request->getSession()->set('companyId',$company->getId());
                            return $this->redirect($this->generateUrl('user_list'));
                        }
                    }
                }
            }
        }
        return array();
    }
}
