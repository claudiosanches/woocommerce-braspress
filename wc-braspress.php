<?php
/**
 * Plugin Name: WooCommerce Braspress
 * Plugin URI: http://claudiosmweb.com/
 * Description: Braspress para WooCommerce
 * Author: claudiosanches
 * Author URI: http://claudiosmweb.com/
 * Version: 0.1
 * License: GPLv2 or later
 * Text Domain: wcbraspress
 * Domain Path: /languages/
 */

define( 'WOO_BRASPRESS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * WooCommerce fallback notice.
 */
function wcbraspress_woocommerce_fallback_notice() {
    $html = '<div class="error">';
        $html .= '<p>' . __( 'WooCommerce Braspress depends on <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> to work!', 'wcbraspress' ) . '</p>';
    $html .= '</div>';

    echo $html;
}

/**
 * SOAP and SimpleXML missing notice.
 */
function wcbraspress_extensions_missing_notice() {
    $html = '<div class="error">';
        $html .= '<p>' . __( 'WooCommerce Braspress depends to <a href="http://php.net/manual/en/book.soap.php">SOAP</a> to work!', 'wcbraspress' ) . '</p>';
    $html .= '</div>';

    echo $html;
}

/**
 * Load functions.
 */
add_action( 'plugins_loaded', 'wcbraspress_shipping_load', 0 );

function wcbraspress_shipping_load() {

    if ( ! class_exists( 'WC_Shipping_Method' ) ) {
        add_action( 'admin_notices', 'wcbraspress_woocommerce_fallback_notice' );

        return;
    }

    if ( ! extension_loaded( 'soap' ) ) {
        add_action( 'admin_notices', 'wcbraspress_extensions_missing_notice' );

        return;
    }

    /**
     * Load textdomain.
     */
    load_plugin_textdomain( 'wcbraspress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    /**
     * wcbraspress_add_method function.
     *
     * @param array $methods
     *
     * @return array
     */
    function wcbraspress_add_method( $methods ) {
        $methods[] = 'WC_Braspress';

        return $methods;
    }

    add_filter( 'woocommerce_shipping_methods', 'wcbraspress_add_method' );

    /**
     * WC_Braspress class.
     */
    class WC_Braspress extends WC_Shipping_Method {

        /**
         * __construct function.
         *
         * @return void
         */
        public function __construct() {
            global $woocommerce;

            $this->id           = 'braspress';
            $this->method_title = __('Braspress', 'wcbraspress');

            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Define user set variables.
            $this->enabled         = $this->settings['enabled'];
            $this->title           = $this->settings['title'];
            $this->availability    = $this->settings['availability'];
            $this->countries       = $this->settings['countries'];
            $this->cnpj            = $this->settings['cnpj'];
            $this->braspress_id    = $this->settings['braspress_id'];
            $this->zip_origin      = $this->settings['zip_origin'];
            $this->display_date    = $this->settings['display_date'];
            $this->additional_time = $this->settings['additional_time'];
            $this->road_shipping   = $this->settings['road_shipping'];
            $this->air_shipping    = $this->settings['air_shipping'];
            $this->debug           = $this->settings['debug'];

            // Active logs.
            if ( 'yes' == $this->debug ) {
                $this->log = $woocommerce->logger();
            }

            // Actions.
            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );
        }

        /**
         * init_form_fields function.
         *
         * @return void
         */
        public function init_form_fields() {
            global $woocommerce;

            $this->form_fields = array(
                'enabled' => array(
                    'title'            => __( 'Enable', 'wcbraspress' ),
                    'type'             => 'checkbox',
                    'label'            => __( 'Enable Braspress', 'wcbraspress' ),
                    'default'          => 'no'
                ),
                'title' => array(
                    'title'            => __( 'Title', 'wcbraspress' ),
                    'type'             => 'text',
                    'description'      => __( 'This controls the title which the user sees during checkout.', 'wcbraspress' ),
                    'default'          => __( 'Braspress', 'wcbraspress' )
                ),
                'availability' => array(
                    'title'            => __( 'Method availability', 'wcbraspress' ),
                    'type'             => 'select',
                    'default'          => 'all',
                    'class'            => 'availability',
                    'options'          => array(
                        'all'          => __( 'All allowed countries', 'wcbraspress' ),
                        'specific'     => __( 'Specific Countries', 'wcbraspress' )
                    )
                ),
                'countries' => array(
                    'title'            => __( 'Specific Countries', 'wcbraspress' ),
                    'type'             => 'multiselect',
                    'class'            => 'chosen_select',
                    'css'              => 'width: 450px;',
                    'default'          => '',
                    'options'          => $woocommerce->countries->countries
                ),
                'cnpj' => array(
                    'title'            => __( 'CNPJ', 'wcbraspress' ),
                    'type'             => 'text',
                    'description'      => __( 'CNPJ registered in the Braspress.', 'wcbraspress' ),
                    'default'          => '',
                ),
                'braspress_id' => array(
                    'title'            => __( 'Braspress ID', 'wcbraspress' ),
                    'type'             => 'text',
                    'description'      => __( 'Your ID in the Braspress.', 'wcbraspress' ),
                    'default'          => '',
                ),
                'zip_origin' => array(
                    'title'            => __( 'Origin Zip Code', 'wcbraspress' ),
                    'type'             => 'text',
                    'description'      => __( 'Zip Code from where the requests are sent.', 'wcbraspress' ),
                    'default'          => '',
                ),
                'display_date' => array(
                    'title'            => __( 'Estimated delivery', 'wcbraspress' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Display date of estimated delivery.', 'wcbraspress' ),
                    'default'          => 'no',
                ),
                'additional_time' => array(
                    'title'            => __( 'Additional days', 'wcbraspress' ),
                    'type'             => 'text',
                    'description'      => __( 'Additional days to the estimated delivery.', 'wcbraspress' ),
                    'default'          => '0',
                ),
                'services' => array(
                    'title'            => __( 'Braspress Services', 'wcbraspress' ),
                    'type'             => 'title',
                    'description'      => '',
                    'default'          => ''
                ),
                'road_shipping' => array(
                    'title'            => __( 'Road', 'wcbraspress' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Road transport.', 'wcbraspress' ),
                    'default'          => 'yes',
                ),
                'air_shipping' => array(
                    'title'            => __( 'Air', 'wcbraspress' ),
                    'type'             => 'checkbox',
                    'description'      => __( 'Air transport.', 'wcbraspress' ),
                    'default'          => 'no',
                ),
                'testing' => array(
                    'title'            => __( 'Testing', 'wcbraspress' ),
                    'type'             => 'title',
                    'description'      => '',
                ),
                'debug' => array(
                    'title'            => __( 'Debug Log', 'wcbraspress' ),
                    'type'             => 'checkbox',
                    'label'            => __( 'Enable logging', 'wcbraspress' ),
                    'default'          => 'no',
                    'description'      => __( 'Log Braspress events, such as WebServices requests, inside <code>woocommerce/logs/braspress.txt</code>.', 'wcbraspress' ),
                )
            );
        }

        /**
         * admin_options function.
         *
         * @return void
         */
        public function admin_options() {
            ?>
            <h3><?php echo $this->method_title; ?></h3>
            <p><?php _e( 'Braspress is a brazilian delivery method.', 'wcbraspress' ); ?></p>
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table>
            <script src="<?php echo plugins_url( 'js/options.js', __FILE__ ); ?>" type="text/javascript"></script>
            <?php
        }

        /**
         * is_available function.
         *
         * @param array $package
         *
         * @return bool
         */
        public function is_available( $package ) {
            global $woocommerce;
            $is_available = true;

            if ( 'no' == $this->enabled ) {
                $is_available = false;
            } else {
                $ship_to_countries = '';

                if ( 'specific' == $this->availability ) {
                    $ship_to_countries = $this->countries;
                } elseif ( 'specific' == get_option( 'woocommerce_allowed_countries' ) ) {
                    $ship_to_countries = get_option( 'woocommerce_specific_allowed_countries' );
                }

                if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
                    $is_available = false;
                }
            }

            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
        }

        /**
         * calculate_shipping function.
         *
         * @param array $package (default: array()).
         *
         * @return void
         */
        public function calculate_shipping( $package = array() ) {
            global $woocommerce;

            echo '<pre>' . print_r( $this->get_quotes( $package ), true ) . '</pre>';

            // $rate = array();

            // $quotes = $this->get_quotes( $package );

            // if ( 'yes' == $this->debug ) {
            //     $this->log->add( 'braspress', 'Braspress WebServices response: ' . print_r( $quotes, true ) );
            // }

            // $list = $this->braspress_services_list();

            // foreach ( $quotes as $key => $value ) {

            //     if ( 0 == $value->Erro ) {

            //         $label = ( 'yes' == $this->display_date ) ? $this->estimating_delivery( $list[$key], $value->PrazoEntrega ) : $list[$key];

            //         array_push(
            //             $rate,
            //             array(
            //                 'id'    => $list[$key],
            //                 'label' => $label,
            //                 'cost'  => $this->fix_format( esc_attr( $value->Valor ) ),
            //             )
            //         );
            //     }
            // }

            // // Register the rate.
            // foreach ( $rate as $key => $value ) {
            //     $this->add_rate( $value );
            // }

        }

        /**
         * Replace comma by dot.
         *
         * @param  mixed $value Value to fix.
         *
         * @return mixed
         */
        private function fix_format( $value ) {
            $value = str_replace( ',', '.', $value );

            return $value;
        }

        /**
         * Fix number data.
         *
         * @param  string $value Number to fix.
         *
         * @return int           Fixed number.
         */
        private function fix_number_data( $value ) {
            $value = str_replace( array( '-', '/', '.', ',', ' ' ), '', $value );

            return $value;
        }

        /**
         * Gets package details.
         *
         * @param  array $package Order contents.
         *
         * @return array          Order total weight, number of items and total price.
         */
        protected function package_details( $package ) {
            $weight = 0;
            $quantity = 0;
            $price = 0;

            foreach ( $package['contents'] as $item_id => $values ) {
                $product = $values['data'];
                $product_qty = $values['quantity'];

                if ( $product_qty > 0 && $product->needs_shipping() ) {
                    $product_weight = woocommerce_get_weight( $this->fix_format( $product->get_weight() ), 'g' );

                    $weight += $product_weight;
                    $quantity += $product_qty;
                    $price += ( $product->get_price() * $product_qty );

                    if ( $product_qty > 1 ) {
                        for ( $i = 0; $i < $product_qty; $i++ ) {
                            $weight += $product_weight;
                        }
                    }
                }
            }

            if ( 'yes' == $this->debug ) {
                $this->log->add( 'braspress', sprintf( 'Total order weight is %sg and the total of items is %s', $weight, $quantity ) );
            }

            return array(
                'weight' => $weight,
                'quantity' => $quantity,
                'price' => $price,
            );
        }

        /**
         * estimating_delivery function.
         *
         * @param string $label
         * @param string $date
         *
         * @return string
         */
        protected function estimating_delivery( $label, $date ) {
            $msg = $label;

            if ( $this->additional_time > 0 ) {
                $date += (int) $this->additional_time;
            }

            if ( $date > 0 ) {
                $msg .= ' (' . sprintf( _n( 'Delivery in %d working day', 'Delivery in %d working days', $date, 'wcbraspress' ),  $date ) . ')';
            }

            return $msg;
        }

        /**
         * Connection Method.
         *
         * @param  [type] $cnpj                 [description]
         * @param  [type] $origin_id            [description]
         * @param  [type] $zip_origin           [description]
         * @param  [type] $zip_destination      [description]
         * @param  [type] $document_destination [description]
         * @param  [type] $type                 [description]
         * @param  [type] $weight               [description]
         * @param  [type] $price                [description]
         * @param  [type] $volume               [description]
         *
         * @return [type]                       [description]
         */
        protected function connection_method(
            $cnpj,
            $origin_id,
            $zip_origin,
            $zip_destination,
            $document_destination,
            $type,
            $weight,
            $price,
            $volume ) {

            // Include Braspress classes.
            include_once WOO_BRASPRESS_PATH . 'classes/Braspress.php';
            include_once WOO_BRASPRESS_PATH . 'classes/BraspressCalculaFrete.php';
            include_once WOO_BRASPRESS_PATH . 'classes/BraspressCalculaFreteResultado.php';

            $quote = array();

            try {
                // Get the quote.
                $request = new BraspressCalculaFrete( $cnpj );
                $request->setIdOrigem( $origin_id );
                $request->setCepOrigem( $zip_origin );
                $request->setCepDestino( $zip_destination );
                $request->setDocumentoDestino( $document_destination );
                $request->setTipoFrete( Braspress::TIPO_FRETE_RODOVIARIO );
                $request->setPeso( $weight );
                $request->setValorNF( $price );
                $request->setVolume( $volume );

                if ( $request->processaConsulta() ) {

                    $result = $request->getResultado();
                    if ( $result->getSucesso() ) {
                        $quote = array(
                            'price' => $result->getTotalFrete(),
                            'time'  => $result->getPrazoEntrega()
                        );

                    } else {
                        if ( 'yes' == $this->debug ) {
                            $this->log->add( 'braspress', sprintf( 'An error has occurred. Message: %s', $result->getMensagemErro() ) );
                        }
                    }
                } else {
                    if ( 'yes' == $this->debug ) {
                        $this->log->add( 'braspress', 'An error occurred, please try again later.' );
                    }
                }

            } catch ( Exception $e ) {
                if ( 'yes' == $this->debug ) {
                    $this->log->add( 'braspress', sprintf( 'An error has occurred while processing your request. Error: %s', $e->getMessage() ) );
                }
            }

            return $quote;
        }

        /**
         * Gets quotes.
         *
         * @param  array $package
         *
         * @return array
         */
        protected function get_quotes( $package ) {
            global $woocommerce;

            $package_details = $this->package_details( $package );
            $order_weight = $package_details['weight'];
            $volume = $package_details['quantity'];
            $price = $package_details['price'];

            $zip_destination = $this->fix_number_data( $package['destination']['postcode'] );

            $document_destination = 00000000000; // Change this later!
            $type = null; // Change this later!

            // Checks if the cart is not just virtual products.
            if ( $order_weight > 0 ) {

                // Get quotes.
                $quote = $this->connection_method(
                    $this->cnpj,
                    $this->braspress_id,
                    $this->zip_origin,
                    $zip_destination,
                    $document_destination,
                    $type,
                    $order_weight,
                    $price,
                    $volume
                );

                return $quote;

            } else {

                // Cart only with virtual products.

                if ( 'yes' == $this->debug ) {
                    $this->log->add( 'braspress', 'Cart only with virtual products.' );
                }

                return array();
            }
        }

    } // class WC_Braspress.

} // function wcbraspress_shipping_load.
