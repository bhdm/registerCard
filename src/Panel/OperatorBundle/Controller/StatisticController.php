<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 25.07.15
 * Time: 15:31
 */

namespace Panel\OperatorBundle\Controller;

use Crm\MainBundle\Entity\Review;
use Crm\MainBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Security("has_role('ROLE_OPERATOR')")
 * @Route("/panel/operator/statistic")
 */
class StatisticController extends Controller
{
    /**
     * @Route("/main", name="statistic_main")
     * Показывает общую статистическую информацию
     */
    public function indexAction(){
        #Количество выпущенных карт по месяцам для графика
        $sql = 'SELECT * FROM car';
        $pdo = $this->getDoctrine()->getManager()->getConnection();
        $st = $pdo->prepare($sql);
        $st->execute();
        $cards = $st->fetchAll();
    }
}