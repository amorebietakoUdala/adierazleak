<?php

namespace App\Controller;

use App\Entity\Indicator;
use App\Form\IndicatorType;
use App\Repository\IndicatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/{_locale}/admin/indicator')]
class IndicatorController extends BaseController
{
    /**
     * Creates or updates a indicator
     */
    #[Route(path: '/new', name: 'indicator_save', methods: ['GET', 'POST'])]
    public function createOrSave(Request $request, IndicatorRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $this->loadQueryParameters($request);
        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator,[
            'readonly' => false,
            'allowedRoles' => $this->getParameter('allowedRoles'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Indicator $data */
            $data = $form->getData();
            if (null !== $data->getId()) {
                $indicator = $repo->find($data->getId());
                $indicator->fill($data);
            }
            $entityManager->persist($indicator);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
            }
            return $this->redirectToRoute('indicator_index');
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->render('indicator/' . $template, [
            'indicator' => $indicator,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * Show the indicator form specified by id.
     * The indicator can't be changed
     */
    #[Route(path: '/{id}', name: 'indicator_show', methods: ['GET'])]
    public function show(Request $request, Indicator $indicator): Response
    {
        $form = $this->createForm(IndicatorType::class, $indicator, [
            'readonly' => true,
            'allowedRoles' => $this->getParameter('allowedRoles'),
        ]);
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'show.html.twig';
        return $this->render('indicator/' . $template, [
            'indicator' => $indicator,
            'form' => $form,
            'readonly' => true
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * Renders the indicator form specified by id to edit it's fields
     */
    #[Route(path: '/{id}/edit', name: 'indicator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Indicator $indicator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IndicatorType::class, $indicator, [
            'readonly' => false,
            'allowedRoles' => $this->getParameter('allowedRoles'),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Indicator $indicator */
            $indicator = $form->getData();
            $entityManager->persist($indicator);
            $entityManager->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('indicator/' . $template, [
            'indicator' => $indicator,
            'form' => $form,
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    #[Route(path: '/{id}/delete', name: 'indicator_delete', methods: ['DELETE'])]
    public function delete(Request $request, Indicator $id, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->get('_token'))) {
            $entityManager->remove($id);
            $entityManager->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('indicator_index');
            } else {
                return new Response(null, \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * List all the indicators
     */
    #[Route(path: '/', name: 'indicator_index', methods: ['GET'])]
    public function index(IndicatorRepository $indicatorRepository, Request $request): Response
    {
        $this->loadQueryParameters($request);
        $ajax = $this->getAjax();
        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator);
        $template = !$ajax ? 'indicator/index.html.twig' : 'indicator/_list.html.twig';
        return $this->render($template, [
            'indicators' => $indicatorRepository->findAll(),
            'form' => $form,
        ]);
    }
}
