<?php

namespace FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AdminBundle\Entity\Goods;
use AdminBundle\Entity\Category;
use AdminBundle\Entity\GoodExtraField;
use AdminBundle\Util\Files\PaginatorUtil;

/**
 * Catalog controller.
 *
 * @Route("/")
 */
class CatalogController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AdminBundle:Category')->findBy(array('parent_category' => null ));
        foreach ($entities as &$entity) {
            $childs = $this->getDoctrine()->getManager()->getRepository('AdminBundle:Category')->getFirstLineChildsCategory($entity->getId());
            if (!is_null($childs)) {
                foreach ($childs as $child) {
                    $entity->addChildCategory($child);
                }
            }
        }
        unset($entity);
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/{id}/goods", name="catalog_view_goods", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function goodsAction(Request $request, $id){

        $per_page = empty($request->query->get('per_page')) ? 10 : $request->query->get('per_page');
        $order = empty($request->query->get('order')) ? 'title' : $request->query->get('order');
        $sc = empty($request->query->get('sc')) ? 'ASC' : $request->query->get('sc');
        $page = empty($request->query->get('page')) ? 1 : $request->query->get('page');
        $search = empty($request->query->get('search')) ? '' : $request->query->get('search');
        $brand = empty($request->query->get('brand')) ? '' : $request->query->get('brand');
        $extra_field = $request->query->get('extra_field');
        $extra_field_types = $request->query->get('extra_field_type');
        $extra_fields = array();
        if (is_array($extra_field) && is_array($extra_field_types)) {
            foreach ($extra_field as $key => $value) {
                $extra_fields[] = array(
                    "id" => $key, 
                    "type" => $extra_field_types[$key], 
                    "val" => $value,
                );
            }
        }
        // exit;
        $em = $this->getDoctrine()->getManager();
        $qty_arr = $em->getRepository('AdminBundle:GoodExtraField')->getQtyGoodsForCatalog($id, $search, $brand, $extra_fields);
        $qty = $qty_arr["qty"];
        $paginator  = new PaginatorUtil($page, $qty, $per_page);
        $page_list = $paginator->getPagesList();
        $page = $paginator->getPage();
        $min_range = count($page_list) ? min($page_list) : 1;
        $max_range = count($page_list) ? max($page_list) : 1;        
        $paginator_arr = array(
            'page_list' => $page_list,
            'min_range' => $min_range,
            'max_range' => $max_range,
            'current' => $page, 
            'total' => $paginator->getTotalPages(), 
        );
        $goods = $em->getRepository('AdminBundle:GoodExtraField')->getGoodsForCatalog($id, $search, $brand, $page, $per_page, $extra_fields, array("order_field" => $order, "sc" => $sc));
        return array(
            'id' => $id,
            'goods' => $goods,
            'paginator' => $paginator_arr, 
        );
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/{id}", name="catalog_view", requirements={"id" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function catalogAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();
        $bread_crumbs = $em->getRepository('AdminBundle:Category')->getParentsCategory($id);
        $extra_fields_values = $em->getRepository('AdminBundle:GoodExtraField')->getExtraFieldsValuesByCategoryId($id);
        $search_form_values = array(
            'show' => array(),
            'hide' => array(),
        );
        $show_it = $search_form_values;
        foreach ($extra_fields_values as $value) {
            if (empty($value['final_value'])) continue;
            $visibility = ( $value['show_it'] || in_array($value['extra_field_id'], $show_it) ) ? 'show' : 'hide';
            if (isset($search_form_values[$visibility][$value['extra_field_id']])) {
                $search_form_values[$visibility][$value['extra_field_id']]['values'][] = $value['final_value'];
            }else{
                $search_form_values[$visibility][$value['extra_field_id']] = array(
                    'values' => array($value['final_value']),
                    'title' => $value['title'],
                    'type' => $value['type'],
                    'id' => $value['extra_field_id'],
                );
            }
        }

        $childs = $em->getRepository('AdminBundle:Category')->getFirstLineChildsCategory($id);
        $brands = $em->getRepository('AdminBundle:Category')->getBrandsForCategory($id);
        foreach ($childs as &$child) {
            $child["goods_qty"] = count($em->getRepository('AdminBundle:Goods')->findBy(array('category' => $child["id"])));
        }
        unset($child);

        return array(
            'childs' => $childs,
            'brands' => $brands,
            'search_form_values' => $search_form_values,
            'id' => $id,
            'bread_crumbs' => $bread_crumbs,
        );
    }

    /**
     * search good by name.
     *
     * @Route("/ajax/search/goods/{string}", name="search_goods")
     * @Method("GET")
     * @Template()
     */
    public function searchByNameAction($string){
        return new JsonResponse( $this->getDoctrine()->getManager()->getRepository('AdminBundle:Goods')->getGoodsByLike($string) );
    }

    /**
     * search good by id.
     *
     * @Route("/ajax/good/{id}", name="catalog_good")
     * @Method("GET")
     * @Template()
     */
    public function goodAction($id){
        $em = $this->getDoctrine()->getManager();
        $good = $em->getRepository('AdminBundle:Goods')->findOneBy(array('id' => $id));
        return array(
            'id' => $id,
            'good' => $good,
        );
    }
}
