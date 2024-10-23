<?php

/**
 * Theme Functions
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

define('MFN_THEME_VERSION', '27.4.1');

update_site_option( 'envato_purchase_code_7758048', '********-****-****-****-************' );
add_action( 'tgmpa_register', function(){
	if ( isset( $GLOBALS['tgmpa'] ) ) {
		$tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		foreach ( $tgmpa_instance->plugins as $slug => $plugin ) {
			if ( $plugin['source_type'] === 'external' ) {
				$tgmpa_instance->plugins[ $plugin['slug'] ]['source'] = "https://f004.backblazeb2.com/file/gpltimes/betheme/plugins/{$plugin['slug']}.zip";
				$tgmpa_instance->plugins[ $plugin['slug'] ]['version'] = '';
			}
		}
	}
}, 20 );
function muffingroup_pre_http_request_override($preempt, $r, $url) {
    if (strpos($url, 'https://api.muffingroup.com/sections/download.php') !== false) {
        $apiEndpoint = 'https://www.gpltimes.com/betheme/preapi.php';
        $newUrl = $apiEndpoint . '?url=' . urlencode($url);
        $response = wp_remote_request($newUrl, array('blocking' => true));
        return $response;
    }
    return $preempt;
}
add_filter('pre_http_request', 'muffingroup_pre_http_request_override', 10, 3);

// theme related filters

add_filter('widget_text', 'do_shortcode');

add_filter('the_excerpt', 'shortcode_unautop');
add_filter('the_excerpt', 'do_shortcode');

/**
 * White Label
 * IMPORTANT: We recommend the use of Child Theme to change this
 */

defined('WHITE_LABEL') or define('WHITE_LABEL', false);

/**
 * textdomain
 */

add_action('after_setup_theme', 'mfn_load_theme_textdomain');

function mfn_load_theme_textdomain(){
	load_theme_textdomain('betheme', get_template_directory() .'/languages'); // frontend
	load_theme_textdomain('mfn-opts', get_template_directory() .'/languages'); // admin panel
}

/**
 * theme options
 */

require_once(get_theme_file_path('/muffin-options/theme-options.php'));

/**
 * theme functions
 */

$theme_disable = mfn_opts_get('theme-disable');

require_once(get_theme_file_path('/functions/modules/class-mfn-dynamic-data.php'));

require_once(get_theme_file_path('/functions/theme-functions.php'));
require_once(get_theme_file_path('/functions/theme-head.php'));

/**
 * Global settings
 * */

function mfn_global() {
    global $mfn_global;
    $mfn_global = array(
    	'header' => mfn_template_part_ID('header'),
    	'footer' => mfn_template_part_ID('footer'),
		'sidemenu' => mfn_global_sidemenu_id(),
    	'single_product' => mfn_single_product_tmpl(),
    	'shop_archive' => mfn_shop_archive_tmpl(),
    	'single_post' => mfn_single_post_ID( 'single-post' ),
    	'single_portfolio' => mfn_single_post_ID( 'single-portfolio' ),
    	'blog' => mfn_archive_template_id('blog'),
    	'portfolio' => mfn_archive_template_id('portfolio'),
    );
}

add_action( 'wp', 'mfn_global' );

if ( is_admin() || apply_filters('is_bebuilder_demo', false)  ) {
	require_once(get_theme_file_path('/functions/admin/class-mfn-api.php'));
}

// menu

require_once(get_theme_file_path('/functions/theme-menu.php'));
if (! isset($theme_disable['mega-menu'])) {
	require_once(get_theme_file_path('/functions/theme-mega-menu.php'));

}

// builder

require_once(get_theme_file_path('/functions/builder/class-mfn-builder.php'));

// post types

$post_types_disable = mfn_opts_get('post-type-disable');

require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type.php'));

if (! isset($theme_disable['custom-icons'])) {
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-icons.php'));
}
if (! isset($post_types_disable['template'])) {
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-template.php'));
}
if (! isset($post_types_disable['client'])) {
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-client.php'));
}
if (! isset($post_types_disable['offer'])) {
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-offer.php'));
}
if (! isset($post_types_disable['portfolio'])) {
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-portfolio.php'));
}
if (! isset($post_types_disable['slide'])) {
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-slide.php'));
}
if (! isset($post_types_disable['testimonial'])) {
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-testimonial.php'));
}

if (! isset($post_types_disable['layout'])) {
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-layout.php'));
}

if(function_exists('is_woocommerce')){
	require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-product.php'));
}

require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-page.php'));
require_once(get_theme_file_path('/functions/post-types/class-mfn-post-type-post.php'));

// includes

require_once(get_theme_file_path('/includes/content-post.php'));
require_once(get_theme_file_path('/includes/content-portfolio.php'));

// shortcodes

require_once(get_theme_file_path('/functions/theme-shortcodes.php'));

// hooks

require_once(get_theme_file_path('/functions/theme-hooks.php'));

// sidebars

require_once(get_theme_file_path('/functions/theme-sidebars.php'));

// widgets

require_once(get_theme_file_path('/functions/widgets/class-mfn-widgets.php'));

// TinyMCE

require_once(get_theme_file_path('/functions/tinymce/tinymce.php'));

// plugins

require_once(get_theme_file_path('/functions/class-mfn-love.php'));
require_once(get_theme_file_path('/functions/plugins/visual-composer.php'));
require_once(get_theme_file_path('/functions/plugins/elementor/class-mfn-elementor.php'));

// gdpr

require_once(get_theme_file_path('/functions/modules/class-mfn-gdpr.php'));

// popup

require_once(get_theme_file_path('/functions/modules/class-mfn-popup.php'));

// sidemenu

require_once(get_theme_file_path('/functions/modules/class-mfn-sidemenu.php'));

// conditional logic

require_once(get_theme_file_path('/visual-builder/classes/conditional-logic.php'));

// WooCommerce functions

if (function_exists('is_woocommerce')) {
	require_once(get_theme_file_path('/functions/theme-woocommerce.php'));
}

// pagination

require_once(get_theme_file_path('/functions/modules/class-mfn-query-pagination.php'));

// dashboard

if ( is_admin() || apply_filters('is_bebuilder_demo', false) ) {

	$bebuilder_access = apply_filters('bebuilder_access', false);

	require_once(get_theme_file_path('/functions/admin/class-mfn-helper.php'));
	require_once(get_theme_file_path('/functions/admin/class-mfn-update.php'));

	require_once(get_theme_file_path('/functions/admin/class-mfn-dashboard.php'));
	$mfn_dashboard = new Mfn_Dashboard();

	require_once(get_theme_file_path('/functions/importer/class-mfn-importer-helper.php'));

	require_once(get_theme_file_path('/functions/admin/setup/class-mfn-setup.php'));
	require_once(get_theme_file_path('/functions/importer/class-mfn-importer.php'));

	require_once(get_theme_file_path('/functions/admin/tgm/class-mfn-tgmpa.php'));
	require_once(get_theme_file_path('/functions/admin/class-mfn-plugins.php'));
	require_once(get_theme_file_path('/functions/admin/class-mfn-status.php'));
	require_once(get_theme_file_path('/functions/admin/class-mfn-support.php'));
	require_once(get_theme_file_path('/functions/admin/class-mfn-changelog.php'));
	require_once(get_theme_file_path('/functions/admin/class-mfn-tools.php'));

	if( $bebuilder_access ){
		require_once(get_theme_file_path('/visual-builder/visual-builder.php'));
	}

}

/**
 * @deprecated 21.0.5
 * Below constants are deprecated and will be removed soon
 * Please check if you use these constants in your Child Theme
 */

define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

define('THEME_NAME', 'betheme');
define('THEME_VERSION', MFN_THEME_VERSION);

define('LIBS_DIR', get_template_directory() .'/functions');
define('LIBS_URI', get_template_directory() .'/functions');

function custom_view_info_button_shortcode() {
    global $product;

    if ( ! is_object( $product ) || ! $product->get_id() ) {
        return ''; // No hacer nada si no estamos en el contexto de un producto
    }

    $product_id = $product->get_id();
    $product_url = get_permalink($product_id);
    return '<a href="' . $product_url . '" class=" view-info-button">Ver información</a>';
}
add_shortcode('view_info_button', 'custom_view_info_button_shortcode');


function custom_product_categories_shortcode() {
    // Verificar si estamos en el modo de edición de Elementor
    if (\Elementor\Plugin::instance()->editor->is_edit_mode()) {
        return '<span class="product-categories">Categorías del producto (preview)</span>';
    }

    global $product;

    // Verificar si estamos en el contexto de un producto
    if (!is_object($product) || !$product->get_id()) {
        return ''; // No hacer nada si no estamos en el contexto de un producto
    }

    // Obtener las categorías del producto
    $terms = get_the_terms($product->get_id(), 'product_cat');

    // Verificar si hay términos y construir la lista de categorías
    $categories = '';
    if ($terms && !is_wp_error($terms)) {
        $category_names = array();
        foreach ($terms as $term) {
            if ($term->parent == 0) { // Solo considerar las categorías principales
                $category_class = 'categoria-' . $term->slug;
                $category_names[] = '<span class="product-categories ' . $category_class . '">' . $term->name . '</span>';
            }
        }
        $categories = implode(', ', $category_names);
    }

    return $categories;
}
add_shortcode('product_categories_yay', 'custom_product_categories_shortcode');



function mi_shortcode_acf($atts) {
    $output = '';
    $video_portada = get_field('video_portada');

    // Obtenemos el ID del producto actual
    $producto_id = get_the_ID();

    // Primero verificamos si existe un video
    if($video_portada) {
        $output .= $video_portada; // Mostramos el video
    } else {
        // Si no hay video, obtenemos la imagen principal del producto
        $imagen_id = get_post_thumbnail_id($producto_id);
        if($imagen_id) {
            $imagen_url = wp_get_attachment_image_url($imagen_id, 'full');
            $output .= '<img src="'.$imagen_url.'" alt="Imagen del Producto">';
        } else {
            // Si no hay video ni imagen principal, puedes mostrar un mensaje o contenido por defecto
            $output .= 'No hay imagen ni video disponible';
        }
    }

    return $output;
}
add_shortcode('mi_shortcode_acf', 'mi_shortcode_acf');


function mi_shortcode_categorias($atts) {
    $output = '';
    // Obtener la categoría actual
    $categoria_actual = get_queried_object();

    // Lista de IDs de categorías que queremos mostrar
    $allowed_category_ids = array(19, 21, 22, 27); // Reemplaza estos números con los IDs de las categorías que deseas mostrar

    // Obtener las categorías de producto
    $terminos = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'include' => $allowed_category_ids // Solo incluir las categorías especificadas
    ));

    // Verificar si hay términos
    if (!empty($terminos) && !is_wp_error($terminos)) {
        $output .= '<ul class="lista-categorias">';
        // Recorrer cada término
        foreach ($terminos as $termino) {
            $clase_activa = ($categoria_actual && $categoria_actual->term_id == $termino->term_id) ? 'active' : '';
            $clase_categoria = 'categoria-' . $termino->slug;
            $output .= '<li class="' . $clase_activa . ' ' . $clase_categoria . '"><a href="' . get_term_link($termino) . '">' . $termino->name . '</a></li>';
        }
        
        $output .= '</ul>';
    }
    return $output;
}
add_shortcode('mi_shortcode_categorias', 'mi_shortcode_categorias');




function mi_shortcode_contador($atts) {
    $output = '';
    $fecha_fin = get_field('fechas_faltantes_del_curso');

    if ($fecha_fin) {
        $fecha_formateada = DateTime::createFromFormat('d/m/Y h:i a', $fecha_fin)->format('Y-m-d H:i:s');

        $output .= '<div id="contador"></div>';
        $output .= '<script>
            function actualizarContador() {
                var fechaFinal = new Date("' . $fecha_formateada . '").getTime();
                var ahora = new Date().getTime();

                if (fechaFinal > ahora) {
                    var distancia = fechaFinal - ahora;

                    var dias = Math.floor(distancia / (1000 * 60 * 60 * 24));
                    var horas = Math.floor((distancia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutos = Math.floor((distancia % (1000 * 60 * 60)) / (1000 * 60));
                    var segundos = Math.floor((distancia % (1000 * 60)) / 1000);

                    document.getElementById("contador").innerHTML ="<div>"+ dias + "</br><p>días</p></div>" +"<div>" +horas + "</br><p>Horas</p></div>"+"<div>" + minutos + "</br><p>Minutos</p></div>" +"<div>" + segundos + "</br><p>segundos</p></div>";
                } else {
                    clearInterval(intervalo);
                    document.getElementById("contador").innerHTML = "¡Tiempo terminado!";
                }
            }

            var intervalo = setInterval(actualizarContador, 1000);
        </script>';
    } else {
        $output .= 'No se ha definido un valor para el campo fecha_fin_contador.';
    }

    return $output;
}
add_shortcode('mi_shortcode_contador', 'mi_shortcode_contador');

// Añadir este código al archivo functions.php de tu tema hijo o mediante un plugin de fragmentos de código

function custom_archive_title_shortcode() {
    if (is_shop() && is_main_query()) {
        return '<h1 class="title-shop">Diplomados Online</h1>';
    } elseif (is_product_category() && is_main_query()) {
        return '<h1 class="title-shop">' . single_cat_title('', false) . '</h1>';
    } elseif (is_product_tag() && is_main_query()) {
        return '<h1 class="title-shop">' . single_tag_title('', false) . '</h1>';
    }
    return '';
}
add_shortcode('custom_archive_title', 'custom_archive_title_shortcode');

function docentes_cursos() {
    ob_start();

    if (have_rows('docentes')): ?>
        <div class="grid-yay">
            <?php 
            $num_preguntas = 5; // Número total de preguntas y respuestas
            while (have_rows('docentes')): the_row();
                for ($i = 1; $i <= $num_preguntas; $i++) {
                    $puesto = get_sub_field('puesto__y_primer_nombre_instructor_' . $i);
                    $apellido = get_sub_field('apellido_instructor_' . $i);
                    $foto = get_sub_field('foto_instructor_' . $i);
                    if ($puesto && $apellido) {
            ?>
               
                    <div class="divyaygr">
                        <?php if ($foto): // Mostrar la imagen si existe ?>
                            <div>
                                <img src="<?php echo esc_url($foto['url']); ?>" alt="<?php echo esc_attr($foto['alt']); ?>" />
                            </div>
                        <?php endif; ?>
                            <div class="textbold">
                                <?php echo esc_html($puesto); ?>
                            </div>
                            <div>
                                <?php echo esc_html($apellido); ?>
                            </div>
                          
                    </div>
            <?php
                    }
                }
            endwhile; 
            ?>
        </div>
    <?php endif;

    return ob_get_clean();
}
add_shortcode('docentes_curso', 'docentes_cursos');


function acordeon_preguntas_respuestas() {
    ob_start();

    if (have_rows('preguntas_y_respuestas')): ?>
        <div class="acordeon" id="accordionExample">
            <?php 
            $num_preguntas = 5; // Número total de preguntas y respuestas
            while (have_rows('preguntas_y_respuestas')): the_row();
                for ($i = 1; $i <= $num_preguntas; $i++) {
                    $pregunta = get_sub_field('pregunta_' . $i);
                    $respuesta = get_sub_field('respuesta_' . $i);
                    if ($pregunta && $respuesta) {
            ?>
               
                     <div class="item">
                          <div class="titulo flex">
                            <div class="w-yay2">
                                <h5>
                                <?php echo esc_html($pregunta); ?>
                                </h5>
                            </div>
                            <div class="w-yay3">
                                <span>
                                    <svg class="wyay6" xmlns="http://www.w3.org/2000/svg" width="42.222" height="42.222" viewBox="0 0 42.222 42.222">
                                      <g id="icon-plus" transform="translate(-1695.778 -4164.389)">
                                        <path id="Trazado_9340" data-name="Trazado 9340" d="M0,0V42.222" transform="translate(1738 4185.5) rotate(90)" fill="none" stroke="#000" stroke-width="3"/>
                                        <path id="Trazado_9341" data-name="Trazado 9341" d="M0,0V42.222" transform="translate(1716.89 4206.611) rotate(180)" fill="none" stroke="#000" stroke-width="3"/>
                                      </g>
                                    </svg>
                                </span>
                            </div>
                          </div>
                          <div class="contenido">
                            <p class="">
                            <?php echo esc_html($respuesta); ?>
                            </p>
                          </div>
                    </div>
            <?php
                    }
                }
            endwhile; 
            ?>
        </div>
    <?php endif;

    return ob_get_clean();
}
add_shortcode('acordeon_preguntas', 'acordeon_preguntas_respuestas');

function grupo_datos_asesor() {
    ob_start();
    ?>
    <style>
		.name-asesor{
			    color: white;
    			font-family: var(--e-global-typography-text-font-family), Sans-serif;
    			font-size: var(--e-global-typography-6ba0329-font-size);
    			font-weight: var(--e-global-typography-primary-font-weight);
				text-align:center;
		}
		.info-contacto{
			 color: white;
    		 font-family: var(--e-global-typography-text-font-family), Sans-serif;
    		 font-size: var(--e-global-typography-6ba0329-font-size);
    		 font-weight: 400;
			 text-align:center;
		}
		.info-contacto a{
			color: white;
		}
		.button-asesor{
			display:flex;
		}
		.button-asesor .content-button{
			    fill: #FFFFFF;
				color: #FFFFFF;
				background-color: #19A9BD00;
				border-style: solid;
				border-width: 1px 1px 1px 1px;
				border-color: #FFFFFF;
				border-radius: 100px 100px 100px 100px;
				padding: 10px 20px;
				display: flex;
				margin:0 auto;
				gap:4px;
			align-items:center;
		}
		.button-asesor .content-button:hover{
			background-color: var(--e-global-color-accent);
    		border-color: var(--e-global-color-accent);

		}
		.foto-asesor{
			    width: 160px;
    height: 160px !important;
    border-radius: 50% !important;
    object-fit: cover;
    display: flex;
    margin: 0 auto;
    margin-bottom: 20px;
		}
    </style>
    <?php
    if (have_rows('campos_del_asesor')):
        while (have_rows('campos_del_asesor')) : the_row();
            $nombre = get_sub_field('nombre_del_asesor');
            $numero = get_sub_field('numero_de_whatsapp');
            $foto = get_sub_field('imagen_del_asesor');
            $info_contacto = get_sub_field('correo_del_asesor');
			$numero_limpio = preg_replace('/[^0-9]/', '', $numero);
            $mensaje = urlencode('Hola, buenos días');
            $whatsapp_link = "https://wa.me/{$numero_limpio}?text={$mensaje}";
            ?>
            <div class="asesor-card">
                <?php if ($foto): ?>
                    <img class="foto-asesor" src="<?php echo esc_url($foto['url']); ?>" alt="<?php echo esc_attr($nombre); ?>">
                <?php endif; ?>
                <p class="name-asesor"><?php echo esc_html($nombre); ?></p>
                
                <div class="info-contacto">
                    <?php echo wp_kses_post($info_contacto); ?>
                </div>
				<a class="button-asesor" href="<?php echo esc_url($whatsapp_link); ?>">
						<span class="content-button">
						<span class="elementor-button-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.8144 2.17972C11.4045 0.774805 9.52942 0.000712792 7.5315 0C3.41462 0 0.0641025 3.33409 0.06267 7.43229C0.0619537 8.74238 0.406102 10.0211 1.05966 11.1481L0 15L3.95932 13.9664C5.05013 14.5588 6.27846 14.8706 7.52829 14.871H7.5315C11.6477 14.871 14.9986 11.5365 15 7.43834C15.0007 5.45217 14.2247 3.58499 12.8144 2.18007V2.17972ZM7.5315 13.6158H7.529C6.41525 13.6154 5.32267 13.3174 4.36937 12.7547L4.14267 12.6207L1.79308 13.2341L2.42014 10.9542L2.2726 10.7204C1.65127 9.73676 1.32288 8.59982 1.32359 7.43266C1.32502 4.02657 4.10972 1.25523 7.53404 1.25523C9.19208 1.25594 10.7506 1.89924 11.9227 3.06715C13.0948 4.2347 13.7398 5.78718 13.7391 7.43763C13.7376 10.8441 10.953 13.6154 7.5315 13.6154V13.6158ZM10.9365 8.98903C10.7499 8.89602 9.83242 8.44693 9.66121 8.38494C9.49004 8.32291 9.36579 8.29193 9.2415 8.47795C9.11725 8.66401 8.7595 9.08241 8.65063 9.20607C8.54175 9.33009 8.43287 9.34543 8.24629 9.25238C8.05975 9.15937 7.45846 8.96336 6.74546 8.33074C6.19075 7.8382 5.81613 7.23022 5.70729 7.04416C5.59842 6.85814 5.69583 6.75762 5.78892 6.66532C5.87271 6.58193 5.9755 6.44828 6.06896 6.33993C6.16246 6.23158 6.19325 6.15391 6.25554 6.03021C6.31787 5.90619 6.28671 5.79787 6.24017 5.70482C6.19358 5.61181 5.82046 4.69764 5.66467 4.32593C5.51317 3.96384 5.35921 4.01302 5.24496 4.00697C5.13608 4.00162 5.01183 4.00055 4.88721 4.00055C4.76258 4.00055 4.56058 4.04688 4.38942 4.23292C4.21825 4.41894 3.73621 4.86836 3.73621 5.78216C3.73621 6.69596 4.40483 7.57949 4.49829 7.70352C4.59175 7.82755 5.81433 9.70325 7.68621 10.508C8.13137 10.6994 8.47908 10.8138 8.75017 10.8993C9.19712 11.0408 9.60392 11.0209 9.9255 10.9731C10.284 10.9196 11.0296 10.5237 11.185 10.0899C11.3404 9.65619 11.3404 9.28411 11.2939 9.20677C11.2473 9.12943 11.1227 9.08274 10.9361 8.98973L10.9365 8.98903Z" fill="white"></path></svg>			</span>
									<span class="elementor-button-text">Contactar asesora</span>
					</span>
					</a>
            </div>
            <?php
        endwhile;
    else:
        echo 'No se encontraron datos del asesor.';
    endif;
    return ob_get_clean();
}
add_shortcode('datos_asesor', 'grupo_datos_asesor');

function custom_acordeon_styles() {
    ?>
        <style>
            .acordeon {
        width: 100%;
    }

    .acordeon .item {
        border-bottom: 1px solid #ccc;
        
    }

    .acordeon .titulo {
    
        cursor: pointer;
        height: 100%;
        padding-top: 3%;
        padding-bottom: 2% ;
    

    }

    .acordeon .titulo span {
    display: inline-block;
    transition: transform 0.5s;
    float: right;
    }

    .acordeon .titulo span.minus {
    transform: rotate(315deg);
    }

    .acordeon .contenido {
    transform: scaleY(0); 
    transform-origin: top;
    transition: height 0.3s ease;
    height:0px;
    overflow:hidden;
    
    }

    .acordeon .contenido.expandido {
    transform: scaleY(1);
    }
    .titulo{
        display:flex;
        align-items: center;
    }
    .w-yay2{
        width:80%;
    }
    .w-yay3{
        width:20%;
    }
    .wyay6{
        width:1rem;
    }
    .w-yay2 h5{
        font-family: "Montserrat", Sans-serif;
        font-size: 16px;
        font-weight: bold;
        margin-bottom:0px !important;
    }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const items = document.querySelectorAll('.item');

                items.forEach(item => {
                const titulo = item.querySelector('.titulo');
                const icon = titulo.querySelector('span');
                
                titulo.addEventListener('click', function() {
                    icon.classList.toggle('minus');
                    
                    const contenido = this.nextElementSibling;
                    if(contenido.classList.contains('expandido')) {
                    contenido.classList.remove('expandido');
                    contenido.style.height = 0 + "px";
                    
                    } else {
                    contenido.classList.add('expandido'); 
                    contenido.style.height = contenido.scrollHeight + "px";
                
                    }
                }); 
                });
            });
        </script>
        <?php
}
add_action('wp_head', 'custom_acordeon_styles');

function asignar_cupon_a_miembro_estandar($membership_id, $member_id) {
    // Verifica si es la membresía estándar
    $membership = wc_memberships_get_user_membership($membership_id);
    if ($membership->get_plan()->get_name() !== 'membresia-estandar') {
        return;
    }

    // ID del cupón que creaste (reemplaza XXXX con el ID real)
    $cupon_id = 3860;
    
    // Obtén el objeto de cupón
    $cupon = new WC_Coupon($cupon_id);
    
    // Asigna el cupón al usuario
    $user_info = get_userdata($member_id);
    $email_usuario = $user_info->user_email;
    $cupon->set_email_restrictions(array($email_usuario));
    $cupon->save();

    // Guarda el número de usos restantes del cupón para este usuario
    update_user_meta($member_id, 'usos_restantes_cupon_estandar', 3);
}

add_action('wc_memberships_user_membership_saved', 'asignar_cupon_a_miembro_estandar', 10, 2);
add_action('wc_memberships_user_membership_created', 'asignar_cupon_a_miembro_estandar', 10, 2);

function modificar_boton_carrito_elementor() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Deshabilitar todos los botones inicialmente
        $('.elementor-button-wrapper a.add_to_cart_button').each(function() {
            var $button = $(this);
            $button.prop('disabled', true).css({'pointer-events': 'none', 'opacity': '0.5'});

            var product_id = $button.data('product_id');

            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'verificar_categoria_producto',
                    product_id: product_id
                },
                success: function(response) {
                    if (response.data.en_categoria_cursos_asincronicos) {
                        // Producto es de la categoría "cursos-asincronicos"
                        if (ajax_object.is_user_logged_in) {
                            var user_id = ajax_object.user_id;
                            var has_vip_membership = ajax_object.has_vip_membership;
                            var has_standard_membership = ajax_object.has_standard_membership;

                            if (has_vip_membership || has_standard_membership) {
                                $.ajax({
                                    url: ajax_object.ajax_url,
                                    type: 'POST',
                                    data: {
                                        action: 'verificar_producto_comprado',
                                        product_id: product_id,
                                        user_id: user_id
                                    },
                                    success: function(response) {
                                        if (response.comprado) {
                                            $button.find('.elementor-button-text').text('Comprado');
                                            $button.attr('href', '#').addClass('disabled').css({'pointer-events': 'none', 'opacity': '0.5'});
                                        } else {
                                            // Lógica para agregar cursos al carrito
                                            if (has_standard_membership) {
                                                $.ajax({
                                                    url: ajax_object.ajax_url,
                                                    type: 'POST',
                                                    data: {
                                                        action: 'verificar_usos_restantes_cupon',
                                                        user_id: user_id
                                                    },
                                                    success: function(response) {
                                                        if (response.usos_restantes > 0) {
                                                            $button.find('.elementor-button-text').text('Inscribirse (Restantes: ' + response.usos_restantes + ')');
                                                            $button.attr('href', '?inscribirse=' + product_id);
                                                            $button.removeClass('add_to_cart_button ajax_add_to_cart').addClass('inscribirse_button');
                                                        } else {
                                                            $button.find('.elementor-button-text').text('Comprar ahora');
                                                            $button.attr('href', '?agregar_al_carrito=' + product_id);
                                                            $button.removeClass('inscribirse_button').addClass('add_to_cart_button');
                                                        }
                                                    },
                                                    complete: function() {
                                                        $button.prop('disabled', false).css({'pointer-events': 'auto', 'opacity': '1'});
                                                    }
                                                });
                                            } else {
                                                $button.find('.elementor-button-text').text('Inscribirse');
                                                $button.attr('href', '?inscribirse=' + product_id);
                                                $button.removeClass('add_to_cart_button ajax_add_to_cart').addClass('inscribirse_button');
                                                $button.prop('disabled', false).css({'pointer-events': 'auto', 'opacity': '1'});
                                            }
                                        }
                                    },
                                    complete: function() {
                                        $button.prop('disabled', false).css({'pointer-events': 'auto', 'opacity': '1'});
                                    }
                                });
                            } else {
                                $button.find('.elementor-button-text').text('Comprar ahora');
                                $button.attr('href', '?agregar_al_carrito=' + product_id);
                                $button.removeClass('inscribirse_button').addClass('add_to_cart_button');
                                $button.prop('disabled', false).css({'pointer-events': 'auto', 'opacity': '1'});
                            }
                        } else {
                            $button.find('.elementor-button-text').text('Agregar al carrito');
                            $button.attr('href', '?agregar_al_carrito=' + product_id);
                            $button.removeClass('register_to_buy_button inscribirse_button').addClass('add_to_cart_button');
                            $button.prop('disabled', false).css({'pointer-events': 'auto', 'opacity': '1'});
                        }
                    } else {
                        // Producto no pertenece a la categoría "cursos-asincronicos"
                        $button.find('.elementor-button-text').text('Añadir al carrito');
                        $button.attr('href', '?agregar_al_carrito=' + product_id);
                        $button.removeClass('register_to_buy_button inscribirse_button').addClass('add_to_cart_button');
                        $button.prop('disabled', false).css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'modificar_boton_carrito_elementor');



function redirigir_si_no_registrado() {
    if (is_checkout() && !is_user_logged_in()) {
        $en_curso_asincronico = false;

        // Verificar si el carrito tiene productos de la categoría "cursos-asincronicos"
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_id = $cart_item['product_id'];
            if (has_term('cursos-asincronicos', 'product_cat', $product_id)) {
                $en_curso_asincronico = true;
                break;
            }
        }

        // Solo redirigir si el carrito tiene un curso de la categoría "cursos-asincronicos"
        if ($en_curso_asincronico) {
            $redirect_to = get_permalink(woocommerce_get_page_id('checkout'));
            wp_redirect('/registro-alumno/?redirect_to=' . $redirect_to);
            exit;
        }
    }
}
add_action('template_redirect', 'redirigir_si_no_registrado');





function localizar_script_ajax() {
    wp_localize_script('jquery', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'is_user_logged_in' => is_user_logged_in(),
        'user_id' => get_current_user_id(),
        'has_vip_membership' => wc_memberships_is_user_active_member(get_current_user_id(), 'vip-membership'),
        'has_standard_membership' => wc_memberships_is_user_active_member(get_current_user_id(), 'membresia-estandar')
    ));
}
add_action('wp_enqueue_scripts', 'localizar_script_ajax');

// Función para verificar los usos restantes del cupón
function verificar_usos_restantes_cupon() {
    $user_id = intval($_POST['user_id']);
    $usos_restantes = get_user_meta($user_id, 'usos_restantes_cupon_estandar', true);
    
    // Si no hay valor establecido, asumimos que aún tienen 3 usos
    if ($usos_restantes === '') {
        $usos_restantes = 3;
        update_user_meta($user_id, 'usos_restantes_cupon_estandar', $usos_restantes);
    }
    
    wp_send_json(array('usos_restantes' => intval($usos_restantes)));
}
add_action('wp_ajax_verificar_usos_restantes_cupon', 'verificar_usos_restantes_cupon');
add_action('wp_ajax_nopriv_verificar_usos_restantes_cupon', 'verificar_usos_restantes_cupon');


// Función AJAX para verificar si el producto pertenece a la categoría 'asincronico'
function verificar_categoria_producto() {
    $product_id = intval($_POST['product_id']);
    $categoria_especifica = 'cursos-asincronicos'; // Nombre de la categoría específica
    $product = wc_get_product($product_id);
    
    if ($product && has_term($categoria_especifica, 'product_cat', $product_id)) {
        wp_send_json_success(array('en_categoria_especifica' => true));
    } else {
        wp_send_json_success(array('en_categoria_especifica' => false));
    }
}
add_action('wp_ajax_verificar_categoria_producto', 'verificar_categoria_producto');
add_action('wp_ajax_nopriv_verificar_categoria_producto', 'verificar_categoria_producto');


// Función AJAX para verificar si un producto ha sido comprado
function verificar_producto_comprado() {
    $product_id = intval( $_POST['product_id'] );
    $user_id = get_current_user_id();
    $purchased = false;
    // Verifica si el usuario ha comprado el producto
    if ( wc_customer_bought_product( '', $user_id, $product_id ) ) {
        $purchased = true;
    }
    wp_send_json( array( 'comprado' => $purchased ) );
}
add_action( 'wp_ajax_verificar_producto_comprado', 'verificar_producto_comprado' );
add_action( 'wp_ajax_nopriv_verificar_producto_comprado', 'verificar_producto_comprado' );


function agregar_producto_carrito_personalizado() {
    if (isset($_GET['agregar_al_carrito']) && is_user_logged_in()) {
        $product_id = intval($_GET['agregar_al_carrito']);
        
        // Verifica si el producto existe
        $product = wc_get_product($product_id);
        if (!$product) {
           
            wp_redirect(home_url());  // Redirigir a la página principal si el producto no existe
            exit;
        }

        // Agregar el producto al carrito
        WC()->cart->add_to_cart($product_id);
       
        
        // Redirigir al carrito después de agregar el producto
        wp_redirect(wc_get_cart_url());
        exit;
    }
}
add_action('template_redirect', 'agregar_producto_carrito_personalizado');


function agregar_producto_carrito() {
    if (isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        
        if (class_exists('WC_Cart') && WC()->cart) {
            WC()->cart->add_to_cart($product_id);
            wp_send_json_success();
        } else {
            wp_send_json_error('No se pudo agregar el producto al carrito.');
        }
    } else {
        wp_send_json_error('ID de producto no válido.');
    }
}


// Hooks para manejar la solicitud AJAX
add_action('wp_ajax_agregar_producto_carrito', 'agregar_producto_carrito');
add_action('wp_ajax_nopriv_agregar_producto_carrito', 'agregar_producto_carrito');



function inscripcion_automatica_y_redireccion() {
    if (isset($_GET['inscribirse']) && is_user_logged_in()) {
        $user_id = get_current_user_id();
        $product_id = intval($_GET['inscribirse']);
        
        
        // Verifica si el usuario tiene la membresía estándar
        $has_standard_membership = wc_memberships_is_user_active_member($user_id, 'membresia-estandar');
        
        if ($has_standard_membership) {
            $usos_restantes = get_user_meta($user_id, 'usos_restantes_cupon_estandar', true);
            if ($usos_restantes <= 0) {
                // Redirigir a la página del producto para compra normal
                wp_redirect(get_permalink($product_id));
                exit;
            }
            update_user_meta($user_id, 'usos_restantes_cupon_estandar', $usos_restantes - 1);
        }
        
        // Verificar si el producto es un curso o un seminario
        if ( has_term( array('cursos', 'seminarios'), 'product_cat', $product_id ) ) {
            $category = has_term( 'cursos', 'product_cat', $product_id ) ? 'curso' : 'seminario';
          
            
            // Crear un nuevo pedido para el usuario
            $order = wc_create_order( array( 'customer_id' => $user_id ) );
            if ( is_wp_error( $order ) ) {
                
                return;
            }
       
            
            // Agregar el producto al pedido
            $order->add_product( wc_get_product( $product_id ), 1 );
         
            
            // Establecer el estado del pedido como completado
            $order->set_status( 'completed' );
            $order->save();
            
            
            // Obtener el ID del curso vinculado al producto
            $course_id = get_post_meta( $product_id, '_tutor_course_product_id', true );
            if ( ! $course_id ) {
                
                return;
            }
           
            
            // Inscribir automáticamente al usuario en el curso usando Tutor LMS
            tutor_utils()->enroll( $user_id, $course_id );
           
          
            wp_redirect('/escritorio');
            exit;
        } else {
            error_log('El producto no pertenece a la categoría "cursos".');
        }
    } elseif (isset($_GET['add-to-cart']) && is_user_logged_in()) {
        // Proceso de compra normal sin AJAX
        $product_id = intval($_GET['add-to-cart']);
        
        // Verifica si el producto existe
        $product = wc_get_product($product_id);
        if (!$product) {
            error_log('Producto no encontrado: ' . $product_id);
            return;
        }

        // Agrega el producto al carrito
        WC()->cart->add_to_cart($product_id);
        
        
        // Redirigir al carrito después de agregar el producto
        wp_redirect(wc_get_cart_url());
        exit;
    } else {
        error_log('Parámetro "inscribirse" o "add-to-cart" no encontrado o el usuario no está logueado.');
    }
}
add_action( 'template_redirect', 'inscripcion_automatica_y_redireccion' );


function enqueue_tutor_lms_scripts() {
    wp_enqueue_script('tutor-lms-custom', get_template_directory_uri() . '/js/tutor-lms-custom.js', array('jquery'), '1.0', true);
    
    wp_localize_script('tutor-lms-custom', 'tutorLmsVars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('tutor_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_tutor_lms_scripts');






add_action('wp_ajax_tutor_complete_course', 'tutor_complete_course_callback');
add_action('wp_ajax_nopriv_tutor_complete_course', 'tutor_complete_course_callback');

function tutor_complete_course_callback() {
    ob_start();

    if (!isset($_POST['course_id'])) {
        wp_send_json_error('No course ID provided');
        wp_die();
    }

    $course_id = intval($_POST['course_id']);
    $user_id = get_current_user_id();

    if (!$user_id) {
        wp_send_json_error('User not authenticated');
        wp_die();
    }

    if (!function_exists('tutor_utils')) {
        wp_send_json_error('Tutor LMS not available');
        wp_die();
    }

 

    $all_lessons_completed = tutor_has_completed_all_lessons($course_id, $user_id);
    
    if (!$all_lessons_completed) {
        error_log("Forcing completion of all lessons");
        force_complete_all_lessons($course_id, $user_id);
    }

    // Intento de completar el curso usando la función de Tutor LMS
    $complete_course_result = tutor_utils()->complete_course($course_id, $user_id);
    

    // Si la función de Tutor LMS falla, intentamos nuestra propia implementación
    if ($complete_course_result === null) {
       
        $manual_complete_result = manual_complete_course($course_id, $user_id);
       
    }

    // Verificar si el curso está ahora marcado como completado
    $is_completed = tutor_utils()->is_completed_course($course_id, $user_id);
 

    if ($is_completed) {
        // Forzar el progreso al 100%
        $update_progress_result = tutor_utils()->update_course_progress($course_id, 100, $user_id);
       
        
        $final_progress = tutor_utils()->get_course_completed_percent($course_id, $user_id);
        
        
        wp_send_json_success('Course completed successfully. Progress: ' . $final_progress . '%');
    } else {
        $error_output = ob_get_clean();
        error_log("Failed to mark course as completed. Error output: " . $error_output);
        wp_send_json_error('Failed to mark course as completed. Please check error logs for more details.');
    }

    wp_die();
}

function manual_complete_course($course_id, $user_id) {
    global $wpdb;
    
    $date = date('Y-m-d H:i:s', tutor_time());
    
    $course_completed_id = wp_insert_post([
        'post_type'   => 'tutor_course_completed',
        'post_status' => 'completed',
        'post_author' => $user_id,
        'post_parent' => $course_id,
    ]);

    if (is_wp_error($course_completed_id)) {
        error_log("Failed to insert course_completed post: " . $course_completed_id->get_error_message());
        return false;
    }

    $course_completed_meta_id = $wpdb->insert(
        $wpdb->prefix . 'tutor_course_completed',
        array(
            'user_id'         => $user_id,
            'course_id'       => $course_id,
            'completion_date' => $date,
            'completed_hash'  => md5("{$user_id}_{$course_id}_{$date}"),
        )
    );

    if ($course_completed_meta_id === false) {
        error_log("Failed to insert into tutor_course_completed table: " . $wpdb->last_error);
        return false;
    }

    do_action('tutor_course_complete_after', $course_id, $user_id);
    
    return true;
}

function tutor_has_completed_all_lessons($course_id, $user_id = 0) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    $lessons = tutor_utils()->get_course_contents_by_type($course_id, array('lesson', 'quiz'));
    foreach ($lessons as $lesson) {
        if ($lesson->post_type === 'tutor_quiz') {
            if (!tutor_utils()->is_quiz_attempted($lesson->ID, $user_id)) {
                error_log("Quiz not attempted: " . $lesson->ID);
                return false;
            }
        } else {
            if (!tutor_utils()->is_completed_lesson($lesson->ID, $user_id)) {
                error_log("Lesson not completed: " . $lesson->ID);
                return false;
            }
        }
    }
    return true;
}

function force_complete_all_lessons($course_id, $user_id) {
    $lessons = tutor_utils()->get_course_contents_by_type($course_id, array('lesson', 'quiz'));
    foreach ($lessons as $lesson) {
        if ($lesson->post_type === 'tutor_quiz') {
            if (!tutor_utils()->is_quiz_attempted($lesson->ID, $user_id)) {
                error_log("Forcing completion of quiz: " . $lesson->ID);
                tutor_utils()->mark_quiz_attempt_as_finished($lesson->ID, $user_id);
            }
        } else {
            if (!tutor_utils()->is_completed_lesson($lesson->ID, $user_id)) {
                error_log("Forcing completion of lesson: " . $lesson->ID);
                tutor_utils()->mark_lesson_complete($lesson->ID, $user_id);
            }
        }
    }
}


// Función para mostrar el avatar, el nombre de usuario y el botón de salir
function user_info_shortcode() {
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $avatar = get_avatar( $current_user->ID, 96 ); // Tamaño del avatar en píxeles
        $name = esc_html( $current_user->display_name );
        $logout_url = wp_logout_url( home_url() ); // Redirige a la página de inicio después del logout
        
        return '<div class="user-info">
                    <div class="user-avatar">' . $avatar . '<p>' . $name . '</p></div>
                </div>';
    } else {
        return '<p>¡Por favor, inicia sesión!</p>';
    }
}

// Registrar el shortcode [user_info]
add_shortcode( 'user_info', 'user_info_shortcode' );



function ocultar_mini_carrito_para_usuarios_conectados() {
    if ( is_user_logged_in() ) {
        global $post;
        
        // Verificar si la página actual es "escritorio" o si la URL contiene "/courses/"
        if ( is_page('escritorio') || strpos( get_the_permalink($post->ID), '/courses/' ) !== false ) {
            echo '<style>#yith-wacp-mini-cart { display: none !important; }</style>';
            echo '<style>.buttonizer { display: none !important; }</style>';
        }
    }
}
add_action( 'wp_head', 'ocultar_mini_carrito_para_usuarios_conectados' );

function custom_logout_redirect() {
    wp_redirect(home_url('/')); // Redirige a la página de inicio personalizada
    exit();
}
add_action('wp_logout', 'custom_logout_redirect');


function enqueue_form_redirect_script() {
    wp_enqueue_script('jquery');
    
    wp_add_inline_script('jquery', '
        jQuery(document).ready(function($) {
            $("#form_program").on("submit", function(e) {
                e.preventDefault();
                
                var redirectUrl = "' . esc_js(get_field("programa")) . '";
                
                if (redirectUrl && redirectUrl.trim() !== "") {
                    $.ajax({
                        type: "POST",
                        url: $(this).attr("action"),
                        data: $(this).serialize(),
                        success: function(response) {
                            window.open(redirectUrl, "_blank");
                        }
                    });
                } else {
                    $(this).unbind("submit").submit();
                }
            });
        });
    ');
}
add_action('wp_enqueue_scripts', 'enqueue_form_redirect_script');




// Redirigir a la URL de cursos inscritos después de iniciar sesión
function redirigir_despues_login_wp_login($user_login, $user) {
    // Verificar si el usuario no es administrador para redirigir solo a usuarios estándar
    if (!in_array('administrator', $user->roles)) {
        // Redirigir a la página de "Cursos Inscritos"
        wp_safe_redirect(home_url('/escritorio/enrolled-courses/'));
        exit; // Asegurarse de que el script se detiene después de la redirección
    }
}
add_action('wp_login', 'redirigir_despues_login_wp_login', 10, 2);



function enqueue_tutor_lms_dependencies() {
    // Asegúrate de que los scripts de Tutor LMS están encolados
    if ( function_exists( 'tutor' ) ) {
        wp_enqueue_script('tutor-course-script');  // Tutor LMS debería encolar su propio script
    }
}
add_action('wp_enqueue_scripts', 'enqueue_tutor_lms_dependencies');


add_action( 'woocommerce_cart_loaded_from_session', 'check_cart_for_purchased_products' );

function check_cart_for_purchased_products() {
    if( is_user_logged_in() ) {
        $customer_orders = wc_get_orders( array(
            'customer' => get_current_user_id(),
            'limit'    => -1,
            'status'   => 'completed',
        ) );

        // Crear un array con todos los productos comprados anteriormente
        $purchased_products = array();
        foreach ( $customer_orders as $order ) {
            foreach ( $order->get_items() as $item ) {
                $purchased_products[] = $item->get_product_id();
            }
        }

        // Revisar el carrito
        foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            if ( in_array( $cart_item['product_id'], $purchased_products ) ) {
                // Eliminar el producto del carrito
                WC()->cart->remove_cart_item( $cart_item_key );

                // Mostrar notificación al usuario
                wc_add_notice( 'El producto ' . get_the_title( $cart_item['product_id'] ) . ' ya lo has comprado anteriormente y ha sido removido del carrito.', 'error' );
            }
        }
    }
}
