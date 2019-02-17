<?php

/**
 * Description of KmjLinkController
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\Controller;

use Kematjaya\StateManagement\Controller\Base\BaseController;
use Kematjaya\StateManagement\Form\KmjLinkType;
use Kematjaya\StateManagement\Filter\KmjLinkFilterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;


class KmjLinkController extends BaseController{
    
    
    public function index(Request $request)
    {
        $queryBuilder = $this->getQueryBuilder($this->container->get("kematjaya.object_manager")->getModelClass("KmjLink"));
        
        $filter = KmjLinkFilterType::class;
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
        return $this->render('@KmjState/link/index.html.twig', array(
            'title' => $this->getTranslator()->trans('state'),
            'pagers' => $paginator,
            'filter' => $form->createView()
        ));
    }
    
    
    public function create(Request $request)
    {
        $kmjLink = $this->container->get("kematjaya.object_manager")->getModel("KmjLink");
        
        $form = $this->createForm(KmjLinkType::class, $kmjLink);
        
        if($this->processForm($form, $request, $this->getTranslator())) {
            return $this->redirectToRoute('kematjaya_link_index');
        }
        return $this->render('@KmjState/link/create.html.twig', array(
            'title' => $this->getTranslator()->trans('link_create'),
            'form' => $form->createView()
        ));
    }
    
    private function processForm(Form $form, Request $request)
    {
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            $kmjLink = $form->getData();
            $type = ($kmjLink->getId()) ? "update": "add";
            if($form->isValid()) {
                
                $em = $this->getDoctrine()->getManager();
                $con = $em->getConnection();
                try{
                    $con->beginTransaction();
                    $em->persist($kmjLink);
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
        $kmjLink = $this->getById($id);
        if(!$kmjLink) {
            $this->addFlash('error', $this->getTranslator()->trans('object.not_found'));
            return $this->redirectToRoute('kematjaya_link_index');
        }
        
        $form = $this->createForm(KmjLinkType::class, $kmjLink);
        
        if ($this->processForm($form, $request, $this->getTranslator())) {
            return $this->redirectToRoute('kematjaya_link_index');
        }
        
        //dump($kmjGroup);exit;
        return $this->render('@KmjState/link/edit.html.twig', [
            'title' => $this->getTranslator()->trans('link_edit'),
            'kmj_group' => $kmjLink,
            'form' => $form->createView()
        ]);
    }
    
    public function delete(Request $request, $id)
    {
        $kmjLink = $this->getById($id);
        if(!$kmjLink) {
            $this->addFlash('error', $this->getTranslator()->trans('object.not_found'));
            return $this->redirectToRoute('kematjaya_link_index');
        }
        if ($this->isCsrfTokenValid('delete'.$kmjLink->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($kmjLink);
            $em->flush();
            $this->addFlash('success', $this->getTranslator()->trans('messages.deleted.success'));
        }else{
            $this->addFlash('error', $this->getTranslator()->trans('messages.deleted.error'));
        }

        return $this->redirectToRoute('kematjaya_link_index');
    }
    
    private function getById($id)
    {
        $kmjLinkClass = $this->container->get("kematjaya.object_manager")->getModelClass("KmjLink");
        return $this->getDoctrine()->getRepository($kmjLinkClass)->find($id);
    }
}
