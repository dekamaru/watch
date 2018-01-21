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
        $em = $this->getDoctrine()->getManager();
        $rig = $em->getRepository('App:Rig')->findOneBy(['privateKey' => $request->get('privateKey', -1)]);

        if ($rig === null)
        {
            return new JsonResponse(['status' => false, 'message' => 'Rig not found']);
        }

        ob_start();
        var_dump($_REQUEST);
        $result = ob_get_clean();
        file_put_contents(__DIR__ . '/../../public/' . $rig->getName() . '.txt', $result . PHP_EOL, FILE_APPEND);
        return new JsonResponse(['status' => true]);
    }
}