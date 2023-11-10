<?php 

/** 
    *InVitro Agencia Digital
    *@link 
    *@package WordPress
    *@subpackage InVitro Agencia Digital
    *@since 1.0.0
    *@version 1.0.0
*/

define('URL', get_stylesheet_directory_uri());
define('IMG', URL.'/images');
define('JS', URL.'/libraries/js');
define('CSS', URL.'/libraries/css');
define('EXTERNAL', URL.'/external');

// Registro de scripts y stylesheets
if(!function_exists ('general_scripts')):
    function general_scripts(){
        wp_enqueue_style('style', get_stylesheet_uri(), array(),'1.0.0','all');        
        wp_enqueue_style('maincss', get_template_directory_uri().'/public/css/app.css','1.0.0','all');        
        wp_enqueue_style('animatecss', CSS.'/animate.min.css', '1.0.0', 'all');
        wp_enqueue_style('splidecss', CSS.'/splide.min.css', '1.0.0', 'all');


        wp_enqueue_script('splidejs', JS.'/splide.min.js',array(),'1.0.0',true);
        wp_enqueue_script('splideautoscrolljs', JS.'/splide-extension-auto-scroll.min.js',array(),'1.0.0',true);
        wp_enqueue_script('mainjs', get_template_directory_uri().'/public/js/main.min.js',array(),'1.0.0',true);
        wp_enqueue_script('wowjs', JS.'/wow.min.js',array(),'1.0.0',true);
    }
endif;
add_action('wp_enqueue_scripts', 'general_scripts');

// Añadir thumbnails a las entradas de WordPress
if ( function_exists( 'add_theme_support' ) )
add_theme_support( 'post-thumbnails' );

// Máximo 30 palabras traidas por el get_the_excerpt()
function my_excerpt_length($length){return 30;}
add_filter('excerpt_length', 'my_excerpt_length');

// Registrar menús
function register_my_menus() {
    register_nav_menus(
        array(
            'header-menu' => __( 'En la cabecera' ),
        )
    );
}
add_action( 'init', 'register_my_menus' );

// Registrar widgets
function register_my_widgets() {
    register_sidebar( array( // dynamic_sidebar('sidebar-principal');
        'name' => __( 'Sidebar principal', 'my_store' ),
        'id' => 'sidebar-principal',
        'description' => __( 'Sidebar de categoría', 'my_store' ),
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => '</li>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>',
    ));
}
add_action( 'widgets_init', 'register_my_widgets' );

// Registro de campos personalizados para secciones que se repiten en toda la web (inc/)
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'menu_title'  => 'Secciones Repetidas',
        'menu_slug'   => 'theme-general-settings',
        'icon_url'    => 'dashicons-table-row-before',
        'redirect'    => false
    ));
    acf_add_options_sub_page(array(
        'page_title'  => 'Primera Sección',
        'menu_title'  => 'Primera Sección',
        'parent_slug' => 'theme-general-settings'
    ));
}

// Funcion para debuggear un array/object
function debuggear($e){
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
}

// TRAYENDO LA ESTRUCTURA DE PRODUCTOS, PASANDOLE PARAMETROS
function getCardProduct($name, $permalink, $image, $description, $regPrice, $salePrice) {
    // Recibe Nombre, Permalink, Thumbnail, Descripcion, Precio Regular, Precio en Oferta

    echo '<div class="card-product">
        <div class="card-product_image">';
            if(!empty($image)){
                echo '<img src="'.$image.'" class="w-100" title="'.$name.'" alt="'.$name.'">';
            }
    echo '</div>
        <div class="card-product_name">
            <h2>'.$name.'</h2>';
            if(!empty($description)){
                echo '<p>'.$description.'</p>';
            }
    echo '</div>';
    echo '</div>';
}

// OBTENIENDO LA CANTIDAD DE PRODUCTOS QUE HAY EN MI CARRITO
function getProductsQuantityInCart() {
    // Verifica si WooCommerce está activo
    if (class_exists('WooCommerce')) {
        // Obtiene la instancia del carrito de WooCommerce
        $carrito = WC()->cart;

        // Obtiene la cantidad de productos en el carrito
        $cantidad_productos = $carrito->get_cart_contents_count();

        return $cantidad_productos < 9 ? $cantidad_productos : '+9';
    } else {
        return 0; // WooCommerce no está activo o no instalado
    }
}

// Agregar el producto al carrito utilizando AJAX
add_action('wp_ajax_add_product_to_cart', 'add_product_to_cart');
add_action('wp_ajax_nopriv_add_product_to_cart', 'add_product_to_cart');
function add_product_to_cart() {
    if (class_exists('WooCommerce')) {
        if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
            $product_id = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);
    
            WC()->cart->add_to_cart($product_id, $quantity);
        }
        wp_die();
    }
}

// Eliminar el producto del carrito utilizando AJAX
add_action('wp_ajax_remove_product_from_cart', 'remove_product_from_cart');
add_action('wp_ajax_nopriv_remove_product_from_cart', 'remove_product_from_cart');
function remove_product_from_cart() {
    if (class_exists('WooCommerce')) {
        if (isset($_POST['cart_item_key'])) {
            $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
            
            // Eliminar el producto del carrito
            WC()->cart->remove_cart_item($cart_item_key);
            
            // Devolver una respuesta (puedes personalizarla según tus necesidades)
            echo 'Producto eliminado del carrito';
        }
        wp_die();
    }
}

// Actualizar el carrito y su estructura utlizando AJAX
add_action('wp_ajax_get_mini_cart', 'get_mini_cart');
add_action('wp_ajax_nopriv_get_mini_cart', 'get_mini_cart');
function get_mini_cart() {
    if (class_exists('WooCommerce')) {
        echo wc_get_template('cart/mini-cart.php');
        wp_die();
    }
}


require_once get_template_directory().'/inc/informacion_general.php';
// require_once get_template_directory().'/inc/modules/productos.php';
// require_once get_template_directory().'/inc/modules/servicios.php';