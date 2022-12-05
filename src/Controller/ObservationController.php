<?php

namespace App\Controller;

use App\Entity\Indicator;
use App\Entity\Observation;
use App\Form\IndicatorSearchType;
use App\Form\ObservationType;
use App\Repository\IndicatorRepository;
use App\Repository\ObservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale}/observation")
 */
class ObservationController extends BaseController
{

    /**
     * @Route("/search/indicator/{indicator}", name="observation_search")
     */
    public function search(Request $request, ObservationRepository $repo, Indicator $indicator): Response {
        $this->loadQueryParameters($request);
        $ajax = $this->getAjax();
        $roles = $this->getUser() !== null ? $this->getUser()->getRoles(): [];
        $requiredRoles = $request->get('roles') ? explode(',',$request->get('roles')) : $roles;
        $observations = $repo->findByIndicatorOrdered($indicator);
        if (!$ajax) {
            $observation = new Observation();
            $roles = $this->getUser() !== null ? $this->getUser()->getRoles(): [];
            $form = $this->createForm(ObservationType::class, $observation,[
                'readonly' => false,
                'locale' => $request->getLocale(),
                'allowedRoles' => $roles,
                'isAdmin' => $this->isGranted("ROLE_ADMIN"),
            ]);
    
            return $this->render('observation/search.html.twig', [
                'observations' => $observations,
                'indicator' => $indicator,
                'form' => $form->createView(),
                'requiredRoles' => $roles,
            ]);
    
        } else {
            return $this->render('observation/_list.html.twig', [
                'observations' => $observations,
                'requiredRoles' => $roles,
            ]);
        }

    }

     /**
     * Creates or updates a observation
     * 
     * @Route("/new", name="observation_save", methods={"GET","POST"})
     */
    public function createOrSave(Request $request, ObservationRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $this->loadQueryParameters($request);
        $observation = new Observation();
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
                $observation = $repo->find($data->getId());
                $observation->fill($data);
            } elseif ($this->checkAlreadyExists($repo, $observation)) {
                $this->addFlash('error', 'messages.observationAlreadyExist');
                $error = true;
            }
            if (!$error) {
                $entityManager->persist($observation);
                $entityManager->flush();
                if ($request->isXmlHttpRequest()) {
                    return new Response(null, 204);
                }
                return $this->redirectToRoute('myObservation_index');
            }
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->render('observation/' . $template, [
            'observation' => $observation,
            'form' => $form->createView(),
        ], new Response(null, $form->isSubmitted() && (!$form->isValid() || $error) ? 422 : 200,));
    }

    /**
     * Renders the observation form specified by id to edit it's fields
     * 
     * @Route("/{id}/edit", name="observation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Observation $observation, EntityManagerInterface $entityManager): Response
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
            $entityManager->persist($observation);
            $entityManager->flush();
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->render('observation/' . $template, [
            'indicator' => $observation,
            'form' => $form->createView(),
            'readonly' => false
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/{id}/delete", name="observation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Observation $id, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$id->getId(), $request->get('_token'))) {
            $entityManager->remove($id);
            $entityManager->flush();
            if (!$request->isXmlHttpRequest()) {
                return $this->redirectToRoute('observation_search', [
                    'indicator' => $id->getIndicator()
                ]);
            } else {
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } else {
            return new Response('messages.invalidCsrfToken', 422);
        }
    }

     /**
     * Show the observation form specified by id.
     * The observation can't be changed
     * 
     * @Route("/{id}", name="observation_show", methods={"GET"})
     */
    public function show(Request $request, Observation $observation): Response
    {
        $form = $this->createForm(ObservationType::class, $observation, [
            'readonly' => true,
            'allowedRoles' => $this->getParameter('allowedRoles'),
            'isAdmin' => $this->isGranted("ROLE_ADMIN"),
        ]);
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'show.html.twig';
        return $this->render('observation/' . $template, [
            'observation' => $observation,
            'form' => $form->createView(),
            'readonly' => true
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200,));
    }

    /**
     * @Route("/", name="myObservation_index")
     */
    public function index(IndicatorRepository $indicatorRepository, ObservationRepository $observationRepository, Request $request): Response
    {
        $this->loadQueryParameters($request);
        $ajax = $this->getAjax();
        $roles = $this->getUser() !== null ? $this->getUser()->getRoles(): [];
        $observation = new Observation();
        $requiredRoles = $request->get('roles') ? explode(',',$request->get('roles')) : $roles;
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
        $indicators = $this->findIndicatorsForRoles($indicatorRepository, $requiredRoles );    

        $lastObservations = [];
        foreach ($indicators as $indicator) {
            $lastObservations[] = $observationRepository->findLastObservationForIndicator($indicator);
        }
        $form = $this->createForm(ObservationType::class, $observation, [
            'readonly' => false,
            'allowedRoles' => $this->getParameter('allowedRoles'),
            'isAdmin' => $this->isGranted("ROLE_ADMIN"),
        ]);
        if (!$ajax) {
            return $this->render('observation/myObservation_index.html.twig', [
                'indicators' => $indicators,
                'observations' => $lastObservations,
                'form' => $form->createView(),
                'searchForm' => $searchForm->createView(),
                'requiredRoles' => $requiredRoles,
            ]);
        } else {
            return $this->render('observation/_myObservation_list.html.twig', [
                'indicators' => $indicators,
                'observations' => $lastObservations,
                'requiredRoles' => $requiredRoles,
            ]);
        }
    }

    private function findIndicatorsForRoles(IndicatorRepository $indicatorRepository, $roles = null) {
        $indicators = [];
        $cleanRoles = null;
        if ( null !== $roles ) {
            $cleanRoles = $this->removeUnnecesaryRoles($roles);
        }
        if ($this->isGranted("ROLE_ADMIN") && count($cleanRoles) === 0 ) {
            $indicators = $indicatorRepository->findAll();
        } elseif (null !== $cleanRoles) {
            $indicators = $indicatorRepository->findByRoles($cleanRoles);
        }
        return $indicators;
    }

    private function removeUnnecesaryRoles($roles) {
        if (($key = array_search('ROLE_USER', $roles)) !== false) {
            unset($roles[$key]);
        }
        if (($key = array_search('ROLE_ADMIN', $roles)) !== false) {
            unset($roles[$key]);
        }
        return $roles;
    }

    private function checkAlreadyExists(ObservationRepository $repo, Observation $observation) {
        $result = $repo->findObservationByExample($observation);
        return $result !== null ? true : false;
    }

}
