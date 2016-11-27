<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AdminBundle\Entity\Extra_fields;
use AdminBundle\Form\Extra_fieldsType;

/**
 * Extra_fields controller.
 *
 * @Route("/administrator/extra_fields")
 */
class Extra_fieldsController extends Controller
{

    /**
     * Lists all Extra_fields entities.
     *
     * @Route("/", name="extra_fields")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AdminBundle:Extra_fields')->getExtraFieldList();
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );

        return array(
            'pagination' => $pagination,
        );
    }
    /**
     * Creates a new Extra_fields entity.
     *
     * @Route("/", name="extra_fields_create")
     * @Method("POST")
     * @Template("AdminBundle:Extra_fields:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Extra_fields();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('extra_fields_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Extra_fields entity.
     *
     * @param Extra_fields $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Extra_fields $entity)
    {
        $form = $this->createForm(new Extra_fieldsType(), $entity, array(
            'action' => $this->generateUrl('extra_fields_create'),
            'method' => 'POST',
            'attr' => array('class'=>'form-horizontal'),
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr' => array('class' => 'btn btn-success'),
            ));

        return $form;
    }

    /**
     * Displays a form to create a new Extra_fields entity.
     *
     * @Route("/new", name="extra_fields_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Extra_fields();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Extra_fields entity.
     *
     * @Route("/{id}", name="extra_fields_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AdminBundle:Extra_fields')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Extra_fields entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Extra_fields entity.
     *
     * @Route("/{id}/edit", name="extra_fields_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AdminBundle:Extra_fields')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Extra_fields entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Extra_fields entity.
    *
    * @param Extra_fields $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Extra_fields $entity)
    {
        $form = $this->createForm(new Extra_fieldsType(), $entity, array(
            'action' => $this->generateUrl('extra_fields_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('class'=>'form-horizontal'),
        ));

        $form->add('submit', 'submit', array(
            'attr' => array('class' => 'btn btn-default'),
            'label' => 'Update'
            ));

        return $form;
    }
    /**
     * Edits an existing Extra_fields entity.
     *
     * @Route("/{id}", name="extra_fields_update")
     * @Method("PUT")
     * @Template("AdminBundle:Extra_fields:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AdminBundle:Extra_fields')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Extra_fields entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('extra_fields_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Extra_fields entity.
     *
     * @Route("/{id}", name="extra_fields_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AdminBundle:Extra_fields')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Extra_fields entity.');
            }
            $em->getRepository('AdminBundle:Extra_fields')->deleteCategoryExtraFieldsLinks($id);
            $em->getRepository('AdminBundle:Extra_fields')->deleteGoodExtraFieldsLinks($id);
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('extra_fields'));
    }

    /**
     * Creates a form to delete a Extra_fields entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('extra_fields_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr' => array('class' => 'btn btn-danger')
                ))
            ->getForm()
        ;
    }
}
