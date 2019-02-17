<?php

/**
 * Description of KmjStateController
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Kematjaya\StateManagement\Controller\Base\BaseController;
use Kematjaya\StateManagement\Form\KmjStateType;
use Kematjaya\StateManagement\Form\KmjStateActionType;
use Kematjaya\StateManagement\Filter\KmjStateFilterType;

class KmjStateController extends BaseController{
    
    public function index(Request $request)
    {
        $queryBuilder = $this->getQueryBuilder($this->container->get("kematjaya.object_manager")->getModelClass("KmjState"));
        
        $filter = KmjStateFilterType::class;
        if($request->get('_reset')) {
            $this->setFilters(null, $filter);
        }
        $form = $this->get('form.factory')->create($filter, $this->getFilters($filter));
        $filters = $request->get($form->getName());
        if ($filters) {
            $form->submit($filters);
            $this->setFilters($filters, $filter);
        }
        $this->getFilterAdapter()->addFilterConditions($form, $queryBuilder);
        
        $paginator = $this->createPaginator($request, $queryBuilder->getQuery());
        return $this->render('@KmjState/state/index.html.twig', array(
            'title' => $this->getTranslator()->trans('state'),
            'pagers' => $paginator,
            'filter' => $form->createView()
        ));
    }
    
    
    public function create(Request $request)
    {
        $kmjState = $this->container->get("kematjaya.object_manager")->getModel("KmjState");
        $form = $this->createForm(KmjStateType::class, $kmjState);
        
        if($this->processForm($form, $request)) {
            return $this->redirectToRoute('kematjaya_state_index');
        }
        return $this->render('@KmjState/state/create.html.twig', array(
            'title' => $this->getTranslator()->trans('state_create'),
            'form' => $form->createView()
        ));
    }
    
    private function processForm(Form $form, Request $request)
    {
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            $kmjState = $form->getData();
            $type = ($kmjState->getId()) ? "update": "add";
            if($form->isValid()) {
                
                $em = $this->getDoctrine()->getManager();
                $con = $em->getConnection();
                try{
                    $con->beginTransaction();
                    $em->persist($kmjState);
                    $em->flush();
                    $con->commit();
                    $this->addFlash('success', $this->getTranslator()->trans('messages.'.$type.'.success'));
                    return true;
                } catch (Exception $ex) {
                    $this->addFlash('error', $this->getTranslator()->trans('messages.'.$type.'.error') . ' : ' . $ex->getMessages());
                    $con->rollBack();
                    return false;
                }
            }else{
                $this->addFlash('error', $this->getTranslator()->trans('messages.'.$type.'.error'));
                return false;
            }
        }
        
        return false;
    }
    
    
    public function edit(Request $request, $id)
    {
        $kmjState = $this->getById($id);
        if(!$kmjState) {
            $this->addFlash('error', $this->getTranslator()->trans('object.not_found'));
            return $this->redirectToRoute('kematjaya_state_index');
        }
        
        $form = $this->createForm(KmjStateType::class, $kmjState);
        
        if ($this->processForm($form, $request)) {
            return $this->redirectToRoute('kematjaya_state_index');
        }
        
        //dump($kmjGroup);exit;
        return $this->render('@KmjState/state/edit.html.twig', [
            'title' => $this->getTranslator()->trans('state_edit'),
            'kmj_group' => $kmjState,
            'form' => $form->createView()
        ]);
    }
    
    
    public function delete(Request $request, $id)
    {
        $kmjState = $this->getById($id);
        if(!$kmjState) {
            $this->addFlash('error', $this->getTranslator()->trans('object.not_found'));
            return $this->redirectToRoute('kematjaya_state_index');
        }
        if ($this->isCsrfTokenValid('delete'.$kmjState->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($kmjState);
            $em->flush();
            $this->addFlash('success', $this->getTranslator()->trans('messages.deleted.success'));
        }else{
            $this->addFlash('error', $this->getTranslator()->trans('messages.deleted.error'));
        }

        return $this->redirectToRoute('kematjaya_state_index');
    }
    
    public function action(Request $request, $id)
    {
        $kmjState = $this->getById($id);
        if(!$kmjState) {
            $this->addFlash('error', $this->getTranslator()->trans('object.not_found'));
            return $this->redirectToRoute('kematjaya_state_index');
        }
        return $this->render("@KmjState/state/action.html.twig", ["title" => $this->getTranslator()->trans("action"), "kmj_state" => $kmjState]);
    }
    
    
    public function addAction(Request $request, $id)
    {
        $kmjState = $this->getById($id);
        if(!$kmjState) {
            $this->addFlash('error', $this->getTranslator()->trans('object.not_found'));
            return $this->redirectToRoute('kematjaya_state_index');
        }
        
        $kmjStateAction = $this->container->get("kematjaya.object_manager")->getModel("KmjStateAction");
        $kmjStateAction->setState($kmjState);
        
        $form = $this->createForm(KmjStateActionType::class, $kmjStateAction);
        //dump($form);exit;
        if ($this->processFormAction($form, $request)) {
            return $this->redirectToRoute('kematjaya_state_action', ["id" => $kmjState->getId()]);
        }
        
        return $this->render("@KmjState/state/form_action.html.twig", 
                [
                    "title" => $this->getTranslator()->trans("add_action"), 
                    "kmj_state" => $kmjState, "form" => $form->createView()]);
    }
    
    
    public function editAction(Request $request, $id)
    {
        $kmjStateAction = $this->getActionById($id);
        if(!$kmjStateAction) {
            $this->addFlash('error', $this->getTranslator()->trans('object.not_found'));
            return $this->redirectToRoute('kematjaya_state_index');
        }
        
        $form = $this->createForm(KmjStateActionType::class, $kmjStateAction);
        
        if ($this->processFormAction($form, $request)) {
            return $this->redirectToRoute('kematjaya_state_action', ["id" => $kmjStateAction->getState()->getId()]);
        }
        
        return $this->render("@KmjState/state/form_action.html.twig", 
                [
                    "title" => $this->getTranslator()->trans("edit_action"), 
                    "kmj_state" => $kmjStateAction->getState(), "form" => $form->createView()]);
    }
    
    private function processFormAction(Form $form, Request $request)
    {
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            $kmjStateAction = $form->getData();
            $type = ($kmjStateAction->getId()) ? "update": "add";
            if($form->isValid()) {
                
                $em = $this->getDoctrine()->getManager();
                $con = $em->getConnection();
                try{
                    $con->beginTransaction();
                    $em->persist($kmjStateAction);
                    $em->flush();
                    $con->commit();
                    $this->addFlash('success', $this->getTranslator()->trans('messages.'.$type.'.success'));
                    return true;
                } catch (Exception $ex) {
                    $this->addFlash('error', $this->getTranslator()->trans('messages.'.$type.'.error') . ' : ' . $ex->getMessages());
                    $con->rollBack();
                    return false;
                }
            }else{
                $this->addFlash('error', $this->getTranslator()->trans('messages.'.$type.'.error'));
                return false;
            }
        }
        return false;
    }
    
    
    public function deleteAction(Request $request, $id)
    {
        $kmjStateAction = $this->getActionById($id);
        if(!$kmjStateAction) {
            $this->addFlash('error', $this->getTranslator()->trans('object.not_found'));
            return $this->redirectToRoute('kematjaya_state_index');
        }
        
        $kmjState = $kmjStateAction->getState();
        if ($this->isCsrfTokenValid('delete'.$kmjStateAction->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($kmjStateAction);
            $em->flush();
            $this->addFlash('success', $this->getTranslator()->trans('messages.deleted.success'));
        }else{
            $this->addFlash('error', $this->getTranslator()->trans('messages.deleted.error'));
        }

        return $this->redirectToRoute('kematjaya_state_action', ["id" => $kmjState->getId()]);
    }
    
    private function getById($id)
    {
        $kmjStateClass = $this->container->get("kematjaya.object_manager")->getModelClass("KmjState");
        return $this->getDoctrine()->getRepository($kmjStateClass)->find($id);
    }
    
    private function getActionById($id)
    {
        $kmjStateActionClass = $this->container->get("kematjaya.object_manager")->getModelClass("KmjStateAction");
        return $this->getDoctrine()->getRepository($kmjStateActionClass)->find($id);
    }
}
