<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionFormType;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionController extends AbstractController
{

    /**
     * @Route("/subscription", name="subscription")
     */
    public function index(Request $request, ValidatorInterface $validator): Response
    {
        $subscriber = new Subscription();
        $form = $this->createForm(SubscriptionFormType::class, $subscriber);
        $form->handleRequest($request);

        $errors = $validator->validate($subscriber);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subscriber);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Your subscription is saved!'
            );
            return $this->redirectToRoute('subscription');


        }

        return $this->render('subscription/subscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function list(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Subscription::class);
        $subscription = $repository->findAll();

        return $this->render('subscription/subscription-list.html.twig', [
            'list' => $subscription,
        ]);
    }

    /**
     * @Route("/subscription/edit/{id}")
     */
    public function update(int $id, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $subscription = $entityManager->getRepository(Subscription::class)->find($id);
        if (!$subscription) {
            throw $this->createNotFoundException(
                'No subscription found for id '.$id
            );
        }
        $form = $this->createFormBuilder($subscription)
            ->add('name')
            ->add('email', EmailType::class)
            ->add('category', ChoiceType::class, [
                    'choices' => [
                        'Media' => 'media',
                        'Politic' => 'politic',
                        'Sport' => 'sport',
                    ]
                ]
            )
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subscription);
            $entityManager->flush();

            return $this->redirectToRoute('subscription_list');
        }

        return $this->render('subscription/subscription.html.twig', [
            'form' => $form->createView(),]);

    }


    /**
     * @Route("/subscription/delete/{id}")
     */
    public function delete(int $id): Response{
        $entityManager = $this->getDoctrine()->getManager();
        $subscription = $entityManager->getRepository(Subscription::class)->find($id);
        if (!$subscription) {
            throw $this->createNotFoundException(
                'No subscription found for id '.$id
            );
        }
        $entityManager->remove($subscription);
        $entityManager->flush();

        return $this->redirectToRoute('subscription_list');
    }
}
