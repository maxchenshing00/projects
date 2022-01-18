<?php
/**
 * Create the relative path for a product image.
 * 
 * @param string $product_name      The product name
 * @param string $product_category  The category the product belongs in
 * 
 * @return string the relative path for the product image
 */
function createImagePath($product_name, $product_category){
    $image_relative_path = 'product-images\\'; //image relative path
    $image_name = str_replace(' ', '-', $product_name);

    if($product_category == "Fish")
    {
        $image_relative_path = $image_relative_path.'marine-fish\\';
    }
    else if($product_category == "Invert")
    {
        $image_relative_path = $image_relative_path.'marine-inverts\\';
    }
    else if($product_category == "Food")
    {
        $image_relative_path = $image_relative_path.'frozen-food\\';
    }
    else if($product_category == "Plant")
    {
        $image_relative_path = $image_relative_path.'fresh-water-plants\\';
    }
    else if($product_category == "Aquascaping")
    {
        $image_relative_path = $image_relative_path.'aquascaping\\';
    }
    else if($product_category == "Aquarium")
    {
        $image_relative_path = $image_relative_path.'aquariums\\';
    }
    else
    {
        $image_relative_path = 'product-images\\';
    }

    if (file_exists($image_relative_path.$image_name.'.png')) 
    {   
        $image_relative_path = $image_relative_path.$image_name.'.png';
    } 
    else if (file_exists($image_relative_path.$image_name.'.jpg')) 
    {
        $image_relative_path = $image_relative_path.$image_name.'.jpg';
    } 
    else {
        $image_relative_path = 'product-images\\';
    }

    return $image_relative_path;
}
?>