<?php

namespace App\Controller;

use App\Entity\Rig;
use App\Entity\Wallet;
use App\Form\RigType;
use App\Form\WalletType;
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
        $createWalletForm = $this->createForm(WalletType::class, null, ['action' => $this->generateUrl('wallet_create')]);
        $em = $this->getDoctrine()->getManager();
        $wallets = $em->getRepository('App:Wallet')->findAll();

        return $this->render('wallet/list.html.twig', [
            'create_form' => $createWalletForm->createView(),
            'wallets' => $wallets,
            'wallet_short_names' => \App\Enum\WalletType::SHORT_NAMES,
            'wallet_pools' => \App\Enum\WalletType::POOLS
        ]);
    }

    /**
     * @Route(path="/wallet/create", name="wallet_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $wallet = new Wallet();
        $createWallet = $this->createForm(WalletType::class, $wallet);
        $createWallet->handleRequest($request);

        if ($createWallet->isSubmitted() && $createWallet->isValid())
        {
            $wallet->updateBalance();
            $em = $this->getDoctrine()->getManager();
            $em->persist($wallet);
            $em->flush();

            $this->addFlash('success', 'Кошелек создан');
        }
        else
        {
            $this->addFlash('danger', 'Не удалось создать кошелек');
        }

        return $this->redirectToRoute('wallet_list');
    }

    /**
     * @Route(path="/wallet/delete/{wallet}", name="wallet_delete")
     * @param Wallet $wallet
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Wallet $wallet)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($wallet);
        $em->flush();

        return $this->redirectToRoute('wallet_list');
    }
}