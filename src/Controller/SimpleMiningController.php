<?php

namespace App\Controller;

use App\Entity\Rig;
use App\Form\RigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimpleMiningController extends Controller
{

    /**
     * @Route(path="/rig/updateStatus", name="sm_rig_update_status")
     */
    public function updateStatusAction(Request $request)
    {
        
    }
}