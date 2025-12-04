<?php

namespace App\Controller;

use App\Entity\Indicator;
use App\Entity\Observation;
use App\Form\IndicatorSearchType;
use App\Form\ObservationType;
use App\Repository\IndicatorRepository;
use App\Repository\ObservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/{_locale}/observation')]
class ObservationController extends BaseController
{

    public function __construct(
        private readonly ObservationRepository $repo, 
        private readonly IndicatorRepository $indicatorRepo, 
        private readonly EntityManagerInterface $em
    ) {}

    #[Route(path: '/search/indicator/{indicator}', name: 'observation_search')]
    public function search(Request $request, Indicator $indicator): Response {
        $this->loadQueryParameters($request);
        $ajax = $this->getAjax();
        $observations = $this->repo->findByIndicatorOrdered($indicator);
        $roles = $this->getUser() !== null ? $this->getUser()->getRoles(): [];
        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation,[
            'readonly' => false,
            'locale' => $request->getLocale(),
            'allowedRoles' => $roles,
            'isAdmin' => $this->isGranted("ROLE_ADMIN"),
        ]);
        $template = $ajax ? 'observation/_list.html.twig' : 'observation/search.html.twig';
        return $this->render($template, [
            'observations' => $observations,
            'indicator' => $indicator,
            'form' => $form,
        ]);

    }

     /**
     * Creates or updates a observation
     */
    #[Route(path: '/new', name: 'observation_save', methods: ['GET', 'POST'])]
    public function createOrSave(Request $request): Response
    {
        $this->loadQueryParameters($request);
        $observation = $this->createObservation($request);
        $roles = $this->getUser() !== null ? $this->getUser()->getRoles(): [];
        $form = $this->createForm(ObservationType::class, $observation,[
            'readonly' => false,
            'locale' => $request->getLocale(),
            'allowedRoles' => $roles,
            'isAdmin' => $this->isGranted("ROLE_ADMIN"),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Observation $data */
            $data = $form->getData();
            $error = false;
            if (null !== $data->getId()) {
                $observation = $this->repo->find($data->getId());
                $observation->fill($data);
            } elseif ($this->checkAlreadyExists($observation)) {
                $this->addFlash('error', 'messages.observationAlreadyExist');
                $error = true;
            }
            if (!$error) {
                $this->em->persist($observation);
                $this->em->flush();
                if ($request->isXmlHttpRequest()) {
                    return new Response(null, Response::HTTP_NO_CONTENT);
                }
                return $this->redirectToRoute('myObservation_index');
            }
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->render('observation/' . $template, [
            'observation' => $observation,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() && (!$form->isValid() || $error) ? 422 : 200,));
    }

    /**
     * Renders the observation form specified by id to edit it's fields
     */
    #[Route(path: '/{id}/edit', name: 'observation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')]  Observation $observation): Response
    {
        $form = $this->createForm(ObservationType::class, $observation, [
            'readonly' => false,
            'allowedRoles' => $this->getParameter('allowedRoles'),
            'isAdmin' => $this->isGranted("ROLE_ADMIN"),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Observation $observation */
            $observation = $form->getData();
            $this->em->persist($observation);
            $this->em->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('observation/' . $template, [
            'indicator' => $observation,
            'form' => $form,
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    #[Route(path: '/{id}/delete', name: 'observation_delete', methods: ['DELETE'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] Observation $id): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->get('_token'))) {
            $this->em->remove($id);
            $this->em->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('observation_search', [
                    'indicator' => $id->getIndicator()
                ]);
            } else {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

     /**
     * Show the observation form specified by id.
     * The observation can't be changed
     */
    #[Route(path: '/{id}', name: 'observation_show', methods: ['GET'])]
    public function show(Request $request, #[MapEntity(id: 'id')] Observation $observation): Response
    {
        $form = $this->createForm(ObservationType::class, $observation, [
            'readonly' => true,
            'allowedRoles' => $this->getParameter('allowedRoles'),
            'isAdmin' => $this->isGranted("ROLE_ADMIN"),
        ]);
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'show.html.twig';
        return $this->render('observation/' . $template, [
            'observation' => $observation,
            'form' => $form,
            'readonly' => true
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    #[Route(path: '/', name: 'myObservation_index')]
    public function index(Request $request): Response
    {
        $this->loadQueryParameters($request);
        $ajax = $this->getAjax();
        $roles = $this->getUser() !== null ? $this->getUser()->getRoles(): [];
        $observation = new Observation();
        $requiredRoles = $request->get('roles') ? explode(',',(string) $request->get('roles')) : $roles;
        $searchForm = $this->createForm(IndicatorSearchType::class, [
            'requiredRoles' => $requiredRoles,
        ], [
            'allowedRoles' => $this->getParameter('allowedRoles'),
        ]);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $requiredRoles = count($data['requiredRoles']) > 0 ? $data['requiredRoles'] : null;
        }
        $indicators = $this->findIndicatorsForRoles($requiredRoles);    

        $lastObservations = [];
        foreach ($indicators as $indicator) {
            $lastObservations[] = $this->repo->findLastObservationForIndicator($indicator);
        }
        $form = $this->createForm(ObservationType::class, $observation, [
            'readonly' => false,
            'allowedRoles' => $this->getParameter('allowedRoles'),
            'isAdmin' => $this->isGranted("ROLE_ADMIN"),
        ]);
        $template = !$ajax ? 'observation/myObservation_index.html.twig' : 'observation/_myObservation_list.html.twig';
        return $this->render($template, [
            'indicators' => $indicators,
            'observations' => $lastObservations,
            'form' => $form,
            'searchForm' => $searchForm,
            'filters' => [
                'roles' => $requiredRoles,
            ],
        ]);
    }

    private function findIndicatorsForRoles($roles = null) {
        $indicators = [];
        $cleanRoles = null;
        if ( null !== $roles ) {
            $cleanRoles = $this->removeUnnecesaryRoles($roles);
        }
        if ($this->isGranted("ROLE_ADMIN") && ( $cleanRoles === null || count($cleanRoles) === 0 ) ) {
            $indicators = $this->indicatorRepo->findAll();
        } elseif (null !== $cleanRoles) {
            $indicators = $this->indicatorRepo->findByRoles($cleanRoles);
        }
        return $indicators;
    }

    private function removeUnnecesaryRoles($roles) {
        if (($key = array_search('ROLE_USER', $roles)) !== false) {
            unset($roles[$key]);
        }
        if (($key = array_search('ROLE_ADIERAZLEAK', $roles)) !== false) {
            unset($roles[$key]);
        }
        if (($key = array_search('ROLE_ADMIN', $roles)) !== false) {
            unset($roles[$key]);
        }
        return $roles;
    }

    private function checkAlreadyExists(Observation $observation) {
        $result = $this->repo->findObservationByExample($observation);
        return $result !== null ? true : false;
    }

    private function createObservation(Request $request) {
        $observation = new Observation();
        if ( $request->get('indicator') ) {
            $observation->setIndicator($this->indicatorRepo->find($request->get('indicator')));
        }
        if ( $request->get('year') ) {
            $observation->setYear($request->get('year'));
        }
        if ( $request->get('month') ) {
            $observation->setMonth($request->get('month'));
        }
        return $observation;
    }

}
