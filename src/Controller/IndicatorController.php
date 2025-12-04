<?php

namespace App\Controller;

use App\Entity\Indicator;
use App\Form\IndicatorType;
use App\Repository\IndicatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/{_locale}/admin/indicator')]
class IndicatorController extends BaseController
{

    public function __construct(
        private IndicatorRepository $repo,
        private EntityManagerInterface $entityManager,
        private IndicatorRepository $indicatorRepository,
    )
    {}
    
    /**
     * Creates or updates a indicator
     */
    #[Route(path: '/new', name: 'indicator_save', methods: ['GET', 'POST'])]
    public function createOrSave(Request $request): Response
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
                $indicator = $this->repo->find($data->getId());
                $indicator->fill($data);
            }
            $this->entityManager->persist($indicator);
            $this->entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response(null, Response::HTTP_NO_CONTENT);
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
    public function show( Request $request, #[MapEntity(id: 'id')] Indicator $indicator): Response
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
    public function edit(Request $request, #[MapEntity(id: 'id')] Indicator $indicator): Response
    {
        $form = $this->createForm(IndicatorType::class, $indicator, [
            'readonly' => false,
            'allowedRoles' => $this->getParameter('allowedRoles'),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Indicator $indicator */
            $indicator = $form->getData();
            $this->entityManager->persist($indicator);
            $this->entityManager->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('indicator/' . $template, [
            'indicator' => $indicator,
            'form' => $form,
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    #[Route(path: '/{id}/delete', name: 'indicator_delete', methods: ['DELETE'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] Indicator $id): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->get('_token'))) {
            $this->entityManager->remove($id);
            $this->entityManager->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('indicator_index');
            } else {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * List all the indicators
     */
    #[Route(path: '/', name: 'indicator_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);
        $ajax = $this->getAjax();
        $indicator = new Indicator();
        $form = $this->createForm(IndicatorType::class, $indicator);
        $template = !$ajax ? 'indicator/index.html.twig' : 'indicator/_list.html.twig';
        return $this->render($template, [
            'indicators' => $this->indicatorRepository->findAll(),
            'form' => $form,
        ]);
    }
}
