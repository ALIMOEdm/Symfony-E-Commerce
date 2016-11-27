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
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Description of XmlEngineController
 *
 * @author alimoedm
 * @Route("/administrator/xml")
 */
class XmlEngineController extends Controller {
    protected $path = '';
    protected $file = 'download.xml';
    
    public function __construct() {
        $this->path = __DIR__.'/../../../web/uploads/xml/';
    }
    
    /**
     * @Route("/download", name="xml_download")
     */
    public function downloadAction(){
        $em = $this->getDoctrine()->getManager();
        $limit = 1000;
        $offset = 0;
        $file_path = $this->path.$this->file;
        if(file_exists($file_path)){
            unlink($file_path);
        }
        $dom = new \DOMDocument('1.0', 'utf-8');
        $file = fopen($file_path, 'a+');
        $goods_element = $dom->createElement('goods');
        while (true){
            $goods = $em->getRepository('AppBundle:Goods')->getGoodSet($limit, $offset);
            if(is_null($goods) || !count($goods)){
                break;
            }
            $offset += count($goods);
            foreach ($goods as $good){
                $g = $dom->createElement('good');
                $short_title = $dom->createElement('short_title', $good['short_title']);
                $g->appendChild($short_title);
                
                $title = $dom->createElement('title', $good['title']);
                $g->appendChild($title);
                
                $xml_title = $dom->createElement('xml_title', $good['good_xml_title']);
                $g->appendChild($xml_title);
                
                $category_xml_title = $dom->createElement('category_xml_title', $good['xml_title']);
                $g->appendChild($category_xml_title);
                
                $description = $dom->createElement('description', $good['description']);
                $g->appendChild($description);
                
                $brand = $dom->createElement('brand', $good['brand']);
                $g->appendChild($brand);
                
                $article = $dom->createElement('article', $good['article']);
                $g->appendChild($article);
                
                $rating = $dom->createElement('rating', $good['rating']);
                $g->appendChild($rating);
                
                $propertes = $em->getRepository('AppBundle:GoodExtraField')->getExtraFiledsByGoodId($good['id']);
                if(count($propertes)){
                    $propertes_element = $dom->createElement("extra_fields");
                    foreach ($propertes as $property){
                        // $extra_field = $dom->createElement("extra_field");
                        
                        $value = '';
                        if($property['type'] == 'text'){
                            $value = $property['value_text'];
                        }else{
                            $value = $property['value_number'];
                        }

                        $extra_field = $dom->createElement($property['xml_title'], $value);

                        // $value = $dom->createElement("value", $property['value']);
                        // $extra_field->appendChild($value);
                        
                        // $value_text = $dom->createElement("value_text", $property['value_text']);
                        // $extra_field->appendChild($value_text);
                        
                        // $value_number = $dom->createElement("value_number", $property['value_number']);
                        // $extra_field->appendChild($value_number);
                        
                        // $type = $dom->createElement("type", $property['type']);
                        // $extra_field->appendChild($type);
                        
                        // $xml_title = $dom->createElement("xml_title", $property['xml_title']);
                        // $extra_field->appendChild($xml_title);
                        
                        $propertes_element->appendChild($extra_field);
                    }
                    
                    $g->appendChild($propertes_element);
                }
                $goods_element->appendChild($g);
            }
        }
        $dom->appendChild($goods_element);
        fwrite($file, $dom->saveXML());
        fclose($file);
        
        $response = new Response();
        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($file_path));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($file_path) . '";');
        $response->headers->set('Content-length', filesize($file_path));

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($file_path));
        return $response;
    }
    
    /**
     * @Route("/upload", name="xml_upload")
     * @Template("AppBundle:Xml:uploadXml.html.twig")
     */
    public function uploadAction(Request $request){
        $temp_file_name = 'temp.xml';
        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
                ->add('file', 'file', array(
                    'label' => '  ',
                ))
                ->getForm();
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $defaultData = $form->getData();
            $file = $defaultData['file'];
            $file->move($this->path, $temp_file_name);
            
            $file_full_path = $this->path.$temp_file_name;
            if(!file_exists($file_full_path)){
                
            }
            
            $dom = new \DOMDocument();
            $xml = file_get_contents($file_full_path);
            $dom->loadXML($xml);
            $goods = $dom->getElementsByTagName('good');
            if(count($goods)){
                foreach ($goods as $good){
                    $good_ent = null;
                    $xml_name = '';
                    foreach ($good->childNodes as $child){
                        if($good->nodeType !== 1){
                            continue;
                        }
                        if(strtolower($child->nodeName) === 'xml_title'){
                            $xml_title = $child->nodeValue;
                            break;
                        }
                    }
                    if($xml_title){
                        $good_ent = $em->getRepository('AppBundle:Goods')->findOneBy(array('xml_title'=>$xml_title));
                    }
                    
                    if(is_null($good_ent)){
                        $good_ent = new Goods();
                    }
//                        $good_ent = new Goods();
                    $category_xml_title = $this->getNodeValue($good, 'category_xml_title');
                    if(!$category_xml_title){
                        continue;
                    }
                    $category_obj = $em->getRepository('AppBundle:Category')->findOneBy(array('xml_title'=>$category_xml_title));
                    if(is_null($category_obj)){
                        continue;
                    }
                    $good_ent->setCategory($category_obj);
                    foreach ($good->childNodes as $child){
                        if($good->nodeType !== 1){
                            continue;
                        }
                        switch (strtolower($child->nodeName)){
                            case 'short_title': 
                                $good_ent->setShortTitle($child->nodeValue);
                                break;
                            case 'title': 
                                $good_ent->setTitle($child->nodeValue);
                                break;
                            case 'xml_title':
                                $good_ent->setXmlTitle($child->nodeValue);
                                break;
                            case 'description':
                                $good_ent->setDescription($child->nodeValue);
                                break;
                            case 'brand': 
                                $good_ent->setBrand($child->nodeValue);
                                break;
                            case 'article': 
                                $good_ent->setArticle($child->nodeValue);
                                break;
                            case 'rating': 
                                $good_ent->setRating($child->nodeValue);
                                break;
                            case 'extra_fields':
                                $good_ent ->removeAllExtraField($em);
                                //смотрим экстра поля, бежим по каждому полю и создаем новые объекты
                                foreach ($child->childNodes as $extra_field){
                                    if($good->nodeType !== 1){
                                        continue;
                                    }

                                    $xml_title_str = $extra_field->nodeName;
                                    $value = $extra_field->nodeValue;


                                    // $xml_title_str = $this->getNodeValue($extra_field, 'xml_title');
                                    if(!$xml_title_str){
                                        continue;
                                    }
                                    //нашли само экстраполе
                                    $extra_field_obj = $em->getRepository('AppBundle:Extra_fields')->findOneBy(array('xml_title'=>$xml_title_str));
                                    if(is_null($extra_field_obj)){
                                        continue;
                                    }

                                    $field_type = $extra_field_obj->getType();

                                    $good_extra_filed = new GoodExtraField();
                                    $good_extra_filed->setExtraField($extra_field_obj);
                                    $good_extra_filed->setGood($good_ent);

                                    // $type_val = $this->getNodeValue($extra_field, 'type');
                                    $good_extra_filed->setType($field_type);
                                    if($field_type === 'text'){
                                        // $text_val = $this->getNodeValue($extra_field, 'value_text');
                                        $good_extra_filed->setValueText($value);
                                    }else{
                                        // $number_val = $this->getNodeValue($extra_field, 'value_number');
                                        $good_extra_filed->setValueNumber($value);
                                    }
                                    $em->persist($good_extra_filed);
                                    $good_ent->addExtraField($good_extra_filed);
                                }
                                break;
                            case 'images':
                                $good_ent->removeAllImages($em);
                                foreach ($child->childNodes as $image){
                                    if($good->nodeType !== 1){
                                        continue;
                                    }
                                    
                                    $img_url = $this->getNodeValue($image, 'url');
                                    
                                    $img = new File();
                                    
                                    $directory = $good_ent->getCategory()->getId().'/'.$good_ent->getId();
                                    DirectoryUtil::createCatalogInUploadDir($directory);
                                    $img->setPath($directory);
                                    
                                    $temp_name = basename($img_url);
                                    $ext = explode('.', $temp_name);
                                    $ext = array_pop($ext);
                                    
                                    $image_name = ImageNameUtill::getImageName($img->getAbsolutePath(), $directory);
                                    $image_name = $good_ent->getArticle().'_'.$image_name.'.'.$ext;
                                    
                                    $img->setName($image_name);
                                    $lfile = fopen($img->getUploadDirWithName(), "w");

                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $img_url);
                                    curl_setopt($ch, CURLOPT_HEADER, 0);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    curl_setopt($ch, CURLOPT_FILE, $lfile);
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                                    curl_exec($ch);
                                    fclose($lfile);
                                    curl_close($ch);
                                    
                                    $img->setGoodId($good_ent);
                                    $em->persist($img);
                                    $good_ent->addImage($img);
                                }
                                break;
                        }
                        $em->persist($good_ent);
                    }
                    $em->flush();
                }
            }
        }
        
        return array(
            'form' => $form->createView(),
        );
    }
    
    public function getNodeValue($parent, $need_node){
        $needs = $parent->getElementsByTagName($need_node);
        $value = '';
        if(!is_null($needs) && count($needs)){
            foreach ($needs as $need){
                $value = $need->nodeValue;
            }
        }
        return $value;
    }
}
