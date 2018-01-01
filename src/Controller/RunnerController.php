<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Run;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RunnerController extends Controller
{
    /**
     * @Route("/")
     * @return Response
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $run = new Run();

        $form = $this->createFormBuilder($run)
            ->add('name', TextType::class)
            ->add('date', TextType::class)
            ->add('time', TextType::class)
            ->add('distance', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Log Run'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $run = $form->getData();
             $em->persist($run);
             $em->flush();

        }

        $runs = $this->getDoctrine()
            ->getRepository(Run::class)
            ->findAll();

        $totalMiles = 0;

        foreach($runs as $run) {
            $totalMiles += $run->getDistance();
        }

        return $this->render('default/run.html.twig', [
            'form'     => $form->createView(),
            'total'    => $totalMiles,
            'pageData' => $runs
        ]);
    }
}