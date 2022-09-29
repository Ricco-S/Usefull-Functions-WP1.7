<?php
function my_theme_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function popup_enqueue_scripts(){
    wp_enqueue_script( 'magnific-popup', ET_BUILDER_URI . '/feature/dynamic-assets/assets/js/magnific-popup.js', array( 'jquery' ), '1.3.0', true );
    wp_enqueue_style('et_jquery_magnific_popup', ET_BUILDER_URI . "/feature/dynamic-assets/assets/css/magnific_popup.css", [], '1.3.0');
}
add_action('wp_enqueue_scripts', 'popup_enqueue_scripts', 20);


function my_custom_scripts() {
    wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array( 'jquery' ),'',true );
}
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );


function getSubCategories($catID, $first=0, $mobile=0){
    $cats = get_terms( array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'parent' => $catID
            ) );
    
    $str=($first==1 && count($cats)>0)?"<div class='subcategories-tree-container ".(($mobile==1)?'mobile':'')."'><h5 class='subcategories-tree-collapse' data-show='".(($mobile==1)?'0':'1')."'>Subcategorias <i class='collapse-icon fa fa-plus' style='float:right'></i><i class='collapsed-icon fa fa-minus' style='float:right'></i></h5>":"";
    if(count($cats)>0){
        
        $str.='<ul class="'.(($first)?'first-level':'').'">';
        foreach( $cats as $cat) {
            $str.="<li><a class='category-tree-link' href='".get_category_link($cat->term_id )."' title='".$cat->name."'>" . $cat->name . "</a>".getSubCategories($cat->term_id)."</li>";
        }
        $str.="</ul>";

    }
    $str.=($first==1 && count($cats)>0)?"</div>":"";
    return $str;
}


function getCategoriaTree($args){
    $cate = get_queried_object();
    $cateID = $cate->term_id;
    
    return getSubCategories($cateID, 1, ((isset($args['mobile']))?$args["mobile"]:0));
}

add_shortcode('category_tree', 'getCategoriaTree');



//-----------------------------------------------------------------------------
// Limit search only for products

// Only show products in the front-end search results
add_filter('pre_get_posts','lw_search_filter_pages');
function lw_search_filter_pages($query) {
    // Frontend search only
    if ( ! is_admin() && $query->is_search() ) {
        $query->set('post_type', 'product');
        $query->set( 'wc_query', 'product_query' );
    }
    return $query;
}

//-----------------------------------------------------------------------------
// adding sku's to product

add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_after_shop_loop_item_sku_in_cart', 20, 1);
function woocommerce_after_shop_loop_item_sku_in_cart( $template )  {
	global $product;
	$sku = $product->get_sku();
	echo "<p class='sku-product-label'>Ref: $sku</p>";
}

function prefix_translate_text( $translated_text ) {
	if ( 'Search results for "%s"' === $translated_text ) {
        $translated_text = 'Resultados para "%s"';
	}else if('Results for "%1$s"' == $translated_text){
	    $translated_text = 'Resultados para "%s"';
	}
	

    return $translated_text;
}
add_filter( 'gettext', 'prefix_translate_text' );


//------------------------------------------------------------------------------





