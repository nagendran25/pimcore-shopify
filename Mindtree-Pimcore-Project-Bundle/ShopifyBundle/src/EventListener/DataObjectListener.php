<?php
// constants are defined in config\pimcore\constants.php

namespace ShopifyBundle\EventListener;


use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Product;


class DataObjectListener 
{
    public function onObjectPostUpdate (DataObjectEvent $e) 
    {
        $obj = $e->getObject();
	
        if ($obj instanceof Product) {
				
			// get all the products from shopify	
			$shopifyapi_url=SHOPIFY_API_URL.'products.json';
			$curl_method='GET';
			$getproduct_list=$this->shopifyCurl($curl_method,'',$shopifyapi_url);
			$getproduct_list=json_decode($getproduct_list);
			$prodcut_array=array();
			foreach ($getproduct_list as $a)
			{
				foreach($a as $b)
				{
					
					
					foreach($b->variants as $c)
					{
					
						$prodcut_array[$b->id]=$c->sku;
					}
					
				}
			}
			
			// check sku in product list
			
			$product_id = array_search($obj->getSku(), $prodcut_array);
			if ($product_id !== false) {
				// update shopify product if sky exist
				$this->updateProductData($this->getProductData($obj),$product_id);
			}
			else
			{
				// create new shopify product if not sky exist
				$this->addProductData($this->getProductData($obj));
				 
				
			}

 	
			
        }
    }
	
	
	//@param $obj product instance
	//@return array
	public function getProductData($obj)
	{
		$product_data = [
		"product" => [ 
			   "title"=> $obj->getProductname(),
				"body_html"=> "<strong>Mindtree Prodcut</strong>",
				"vendor"=> "Mindtree",
				"product_type"=>$obj->getProducttype(),
			"variants" => [
					[           
					   "price"=>$obj->getPrice(),
					   "sku"=> $obj->getSku(),
					  
					]
			],
		
		]
		];
		
		return $product_data;
		
	}
	//@param $obj product array
	//@return array
	public function addProductData($productArray)
	{
		$shopifyapi_url=SHOPIFY_API_URL.'products.json';
        $curl_method='POST';
		$addproduct=$this->shopifyCurl($curl_method,$productArray,$shopifyapi_url);
		return $addproduct;
		
	}
	//@param $obj product array
	//@param $productId integer
	//@return array
	public function updateProductData($productArray,$productId)
	{
		$shopifyapi_url=SHOPIFY_API_URL.'/products/'.$productId.'.json';
        $curl_method='PUT';
		$updateproduct=$this->shopifyCurl($curl_method,$productArray,$shopifyapi_url);
		return $updateproduct;
		
	}
	
	//@param $apimenth string 
	//@param $apidata array
	//@param $apiurl string
	//@return array
   public function shopifyCurl($apimethod,$apidata='',$apiurl)
   {
			if(empty($apimethod))
			{
				$apimethod='GET';
			}
			if(empty($apiurl))
			{
				$apiurl=SHOPIFY_API_URL.'products.json';
			}
	
	    $curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL =>$apiurl,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST =>$apimethod,
		CURLOPT_HTTPHEADER => array(
		'X-Shopify-Access-Token:'.SHOPIFY_ACCESS_TOKEN, // Get access token from constants
		'Content-Type: application/json'
		),
		));
		if($apimethod!='GET'){
			
			 curl_setopt_array($curl, array(
  
             CURLOPT_POSTFIELDS => json_encode($apidata),

              ));
		}

		$response = curl_exec($curl);

		 curl_close($curl);
	     return $response;
	
	
    }
}