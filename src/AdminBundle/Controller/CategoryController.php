<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AdminBundle\Entity\Category;
use AdminBundle\Entity\Goods;
use AdminBundle\Form\CategoryType;

/**
 * Category controller.
 *
 * @Route("/administrator/category")
 */
class CategoryController extends Controller
{

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="category")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AdminBundle:Category')->findAll();
        
        if(count($entities)){
            foreach ($entities as $entity){
                $entity->setParentCategoryEntity2($this->getParentCategoryEntity($entity->getParentCategory()));
            }
        }

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Category entity.
     *
     * @Route("/", name="category_create")
     * @Method("POST")
     * @Template("AdminBundle:Category:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Category();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $appbundle_category = $request->get('appbundle_category');
            $parent_category = isset($appbundle_category['parent_category']) ? $appbundle_category['parent_category'] : 0;
            $this->addTreePaths($parent_category, $entity->getId());

            return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Category $entity)
    {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_create'),
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
     * Displays a form to create a new Category entity.
     *
     * @Route("/new", name="category_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AdminBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $entity->setParentCategoryEntity2($this->getParentCategoryEntity($entity->getParentCategory()));
        
        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    public function getParentCategoryEntity($parent_category_id){
        $em = $this->getDoctrine()->getManager();
        $parent_category = null;
        if(!is_null($parent_category_id) && $parent_category_id){
            $parent_category = $em->getRepository('AdminBundle:Category')->find($parent_category_id);
        }
        return $parent_category;
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AdminBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        if ( null !== $entity->getParentCategory() ){
            $entity->setParentCategoryEntity($em->getRepository('AdminBundle:Category')->find($entity->getParentCategory()));
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
    * Creates a form to edit a Category entity.
    *
    * @param Category $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Category $entity)
    {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('class'=>'form-horizontal'),
        ));

        $form->add('submit', 'submit', array(
            'attr' => array('class' => 'btn btn-default'),
            'label' => 'Изменить'
            ));

        return $form;
    }
    /**
     * Edits an existing Category entity.
     *
     * @Route("/{id}", name="category_update")
     * @Method("PUT")
     * @Template("AdminBundle:Category:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AdminBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->updTreePaths($request, $id);
            return $this->redirect($this->generateUrl('category_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    protected function deleteTreePaths($id){
        $result = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Category')->deleteCategoriesByDescendant($id);
    }

    protected function updTreePaths($request, $id){
        $appbundle_category = $request->get('appbundle_category');
        $parent_category = isset($appbundle_category['parent_category']) ? $appbundle_category['parent_category'] : 0;
        $this->addTreePaths($parent_category, $id);
        $em = $this->getDoctrine()->getManager();
        $childs = $em->getRepository('AdminBundle:Category')->getFirstLineChildsCategory($id);
        if (!is_null($childs)) {
            $this->handleTreePaths($id, $childs);
        }
    }
    
    protected function handleTreePaths($category_id, $childs){
        foreach ($childs as $child) {
            $em = $this->getDoctrine()->getManager();
            $next_childs = $em->getRepository('AdminBundle:Category')->getFirstLineChildsCategory($child['id']);
            if (!is_null($next_childs)) {
                $this->addTreePaths($category_id, $child['id']);
                $this->handleTreePaths($child['id'], $next_childs);
            }
        }
    }

    protected function addTreePaths($parent_category, $id){
        $this->deleteTreePaths($id);
        $result = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Category')->addCategoryTreePaths($id, $parent_category);
    }

    
    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AdminBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }
            $this->deleteTreePaths($id);
            $em->getRepository('AdminBundle:Goods')->updateGoodsCategory($id);
            $em->getRepository('AdminBundle:Category')->deleteExtraFieldsLinks($id);
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('category'));
    }

    /**
     * Creates a form to delete a Category entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Удалить',
                'attr' => array('class' => 'btn btn-danger')
                ))
            ->getForm()
        ;
    }
}
