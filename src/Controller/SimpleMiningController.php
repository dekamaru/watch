<?php

namespace App\Controller;

use App\Entity\Rig;
use App\Entity\RigStat;
use App\Enum\MiningType;
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

        $stat = $rig->getStatistics() ?? new RigStat();
        $stat->setPublicIp($request->getClientIp());
        $type = MiningType::ZEC;
        $data = json_decode($request->get('statusJson'), true);

        if (!empty($request->get('statusClaymore')))
        {
            $data['claymore'] = json_decode($request->get('statusClaymore'), true);
            $type = MiningType::ETH;
        }

        $stat->load($data, $type);

        $rig->setStatistics($stat);
        $em->persist($rig);
        $em->flush();

        return new JsonResponse(['status' => true]);
    }
}