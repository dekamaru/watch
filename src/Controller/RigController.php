<?php

namespace App\Controller;

use App\Entity\Rig;
use App\Form\RigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RigController extends Controller
{

    /**
     * @Route(path="/rig/list", name="rig_list")
     */
    public function listAction(Request $request)
    {
        $createRigForm = $this->createForm(RigType::class, null, ['action' => $this->generateUrl('rig_create')]);
        $em = $this->getDoctrine()->getManager();
        $rigs = $em->getRepository('App:Rig')->findBy([], ['name' => 'ASC']);

        return $this->render('rig/list.html.twig', [
            'create_form' => $createRigForm->createView(),
            'rigs' => $rigs
        ]);
    }

    /**
     * @Route(path="/rig/create", name="rig_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $rig = new Rig();
        $createRig = $this->createForm(RigType::class, $rig);
        $createRig->handleRequest($request);

        if ($createRig->isSubmitted() && $createRig->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rig);
            $em->flush();

            $this->addFlash('success', 'Ферма добавлена');
            return $this->redirectToRoute('rig_list');
        }
        else
        {
            $this->addFlash('danger', 'Не удалось создать ферму');
            return $this->redirectToRoute('rig_list');
        }
    }

    /**
     * @Route(path="/rig/availability/{rig}", name="rig_update")
     */
    public function updateAvailabilityAction(Request $request, Rig $rig)
    {
        $em = $this->getDoctrine()->getManager();
        $rig->checkAvailability();
        $em->persist($rig);
        $em->flush();

        return $this->redirectToRoute('rig_list');
    }
}