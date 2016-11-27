<?php
namespace AppBundle\Controller;

use AppBundle\Entity\File;
use AppBundle\Entity\GoodExtraField;
use AppBundle\Entity\Goods;
use AppBundle\Form\GoodsType;
use AppBundle\Util\Files\DirectoryUtil;
use AppBundle\Util\Files\ImageNameUtill;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


/**
 * Goods controller.
 *
 * @Route("/administrator/good")
 */
class GoodController extends Controller{

    /**
     * @Route("/", name="good")
     * @Method("GET")
     * @Template("AppBundle:Good:index.html.twig")
     * @return array
     */

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Goods')->getGoodList();
        
//        $search_str = $request->query->get('search', '');
//        if($search_str){
//            $session->set('search_str', $search_str);
//        }elseif($session->get('search_str') != null){
//            $search_str = $session->get('search_str');
//        }
                
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );
        return array(
//            'goods' => $goods,
            'pagination' => $pagination,
        );
    }

    /**
     * @Route("/new", name="good_new")
     * @Route("/{id}/edit", name="good_edit")
     * @Template("AppBundle:Good:edit.html.twig")
     */
    public function createAction(Request $request, $id = '')
    {
        $em = $this->getDoctrine()->getManager();

        $task = '';
        $deleteForm = "";
        if(!$id){
            $task = 'new';
            $good = new Goods();
            $form_action = $this->generateUrl('good_new');
        }else{
            $task = 'edit';
            $good = $em->getRepository('AppBundle:Goods')->findOneBy(array('id' => $id));
            if(is_null($good)){
                throw new EntityNotFoundException("Такого товара не существет");
            }
            $deleteForm = $this->createDeleteForm($id);
            $deleteForm = $deleteForm->createView();
            $form_action = $this->generateUrl('good_edit', array('id' => $good->getId()));
        }

        $form = $this->createForm(new GoodsType(), $good);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $good = $form->getData();
            $em->persist($good);
            $em->flush();
            $files = json_decode($good->getImagesHiddenField());

            if(count($files) && !is_null($files)){
                foreach($files as $file){
                    $file_str = explode(',', $file->file);
                    $ext = explode('.', $file->name);
                    $ext = array_pop($ext);
                    $number = 0;

                    $image = new File();
                    $image->setGoodId($good);
                    if($good->getCategoryCache()){
                        $directory = $good->getCategoryCache().'/'.$good->getId();
                    }
                    else{
                        $directory = $good->getCategory()->getId().'/'.$good->getId();
                    }

                    $image->setPath($directory);

                    $absolute_path = $image->getAbsolutePath();
                    $image_numeric = ImageNameUtill::getImageName($absolute_path, $directory);
//                    if ($handle = @opendir($absolute_path)) {
//                        $nums = array();
//                        while (false !== ($entry = readdir($handle))) {
//                            if ($entry != "." && $entry != "..") {
//                                $ext_2 = explode('.', $entry);
//                                $name_parts = explode('_', $ext_2[0]);
//                                $num = $name_parts[1];
//                                $nums[] = $num;
//                            }
//                        }
//                        while(true){
//                            if( in_array($image_numeric, $nums)){
//                                $image_numeric++;
//                                continue;
//                            }
//                            break;
//                        }
//                        closedir($handle);
//                    }else{
//                        DirectoryUtil::createCatalogInUploadDir($directory);
//                    }
                    $image_name = $good->getArticle().'_'.$image_numeric.'.'.$ext;
                    $image->setName($image_name);
                    $good->addImage($image);
                    file_put_contents($image->getUploadDirWithName(), base64_decode($file_str[1]));
                    $em->persist($image);
                }
            }
            if($good->getCategoryCache()){
                if($good->getCategoryCache() != $good->getCategory()->getId()){
                    $directory = $good->getCategory()->getId().'/'.$good->getId();
                    DirectoryUtil::createCatalogInUploadDir($directory);
                    $images = $good->getImages();
                    if(count($images)){
                        foreach($images as $im){
                            $old_path = $im->getUploadDirWithName();
                            if(file_exists($old_path)) {
                                $im->setPath($directory);
                                if (copy($old_path, $im->getUploadDirWithName())) {
                                    $em->persist($im);
                                }
                                @unlink($old_path);
                            }
                        }
                    }
                    $good->setCategoryCache($good->getCategory()->getId());
                }
            }else{
                $good->setCategoryCache($good->getCategory()->getId());
            }

            $extra_fields = json_decode($good->getExtraFieldsCache(), true);
            if(!is_null($extra_fields) && count($extra_fields)){
                $good ->removeAllExtraField($em);
                foreach($extra_fields as $field){
                    $extra_field = $em->getRepository('AppBundle:Extra_fields')->find($field['id']);
                    $good_extra_field = new GoodExtraField();
                    $good_extra_field->setExtraField($extra_field);
                    $good_extra_field->setGood($good);
                    $good_extra_field->setValue($extra_field->getType(), $field['value']);
                    $em->persist($good_extra_field);
                }
            }
            $em->persist($good);
            $em->flush();

            $url = $this->generateUrl('good_edit', array('id' => $good->getId()));
            $response = new RedirectResponse($url);
            return $response;
        }

        return array(
            'task' => $task,
            'form' => $form->createView(),
            'good' => $good,
            'form_action' => $form_action,
            'delete_form' => $deleteForm,
        );
    }

    /**
     * @Route("/{id}", name="good_show")
     * @Template("AppBundle:Good:show.html.twig")
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $good = $em->getRepository('AppBundle:Goods')->findOneBy(array('id' => $id));
        if(is_null($good)){
            throw new EntityNotFoundException("Такого товара не существет");
        }

        return array(
            'good' => $good,
        );

    }

    /**
     * @Route("/category/info/get-extra-fields", name="get_category_extra_fields")
     */
    public function getCategoryExtraField(Request $request){

        $category_id = $request->request->get('category_id', 0);
        $good_id = $request->request->get('id', '');

        $em = $this->getDoctrine()->getManager();
        //ищем родительские категории для текущей категории
        $set_of_catigories = $em->getRepository('AppBundle:Category')->getParentsCategory($category_id);
        $good = null;
        if($good_id){
            //если мы изменяем существующий товар, ищем его
            $good = $em->getRepository('AppBundle:Goods')->findOneBy(array('id' => $good_id));
            if(is_null($good)){
                throw new EntityNotFoundException("Такого товара не существет");
            }
        }
        $extra_fields = new ArrayCollection();
        //идем по каждой категории
        foreach($set_of_catigories as $cat_id){
            $category = $em->getRepository('AppBundle:Category')->find($cat_id['id']);
            $extra_fs = $category->getExtraFields();
            //если у нее есть экстраполя
            if(count($extra_fs)){
                //для каждого поля
                foreach($extra_fs as $f){
                    if(!$extra_fields->contains($f)){
                        if(!is_null($good)){
                            $gef = $em->getRepository('AppBundle:GoodExtraField')->getExtraFiledValBayGoodIdAndFieldId($good->getId(), $f->getId());
                            if(!is_null($gef)) {
                                $f->setValue($gef->getValue($f->getType()));
                            }
                        }
                        $extra_fields->add($f);
                    }
                }
            }
        }
        return new JsonResponse(
            array('html' => $this->renderView(
                'AppBundle:Good:extra_field_list.html.twig',
                array(
                    'extra_fields' => $extra_fields,
                )
            ))
        );
    }
 
    /**
     * @Route("/{good_id}/get-photo/{photo_name}", name="get_good_photo")
     */
    public function getPhoto($good_id, $photo_name){
        $em = $this->getDoctrine()->getManager();
        $good_photo = $em->getRepository('AppBundle:File')->findOneBy(array('good_id' => $good_id, 'name' => $photo_name));
        if(is_null($good_photo)){
            return new JsonResponse(
                array(
                    'error' => 1,
                    'message' => 'Can not find fish photo',
                )
            );
        }
        $file_path = $good_photo->getUploadDirWithName();
        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($file_path));

        // Send headers before outputting anything
        $response->sendHeaders();
        $response->setContent(base64_encode(readfile($file_path)));
        return $response;
    }

    /**
     * @Route("/{good_id}/remove-photo/{photo_name}", name="remove_good_photo")
     */
    public function removeGoodImage($good_id, $photo_name){
        $em = $this->getDoctrine()->getManager();
        $good_photo = $em->getRepository('AppBundle:File')->findOneBy(array('good_id' => $good_id, 'name' => $photo_name));
        if(is_null($good_photo)){
            return new JsonResponse(
                array(
                    'error' => 1,
                    'message' => 'Can not find fish photo',
                )
            );
        }
        $em->remove($good_photo);
        $em->flush();
        return new JsonResponse(
            array(
                'error' => 0,
            )
        );
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
            ->setAction($this->generateUrl('good_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr' => array('class' => 'btn btn-danger')
                ))
            ->getForm()
        ;
    }

    /**
     * Deletes a Extra_fields entity.
     *
     * @Route("/{id}", name="good_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Goods')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Extra_fields entity.');
            }
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('good'));
    }
}