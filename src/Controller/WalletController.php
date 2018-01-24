<?php

namespace App\Controller;

use App\Entity\Rig;
use App\Form\RigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WalletController extends Controller
{

    /**
     * @Route(path="/wallet/list", name="wallet_list")
     */
    public function listAction(Request $request)
    {
        return new Response('Not implemented :(');
    }
}