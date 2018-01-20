<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        return $this->render('dashboard/index.html.twig');
    }
}