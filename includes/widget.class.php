<?php
/**
 * Выводит последние посты в виджете
 */
class WidgetBasketAVK extends WP_Widget {
    
    
	public function __construct() {
        global $AVKShopEngine;
		$widget_ops = array(
            'classname' => $AVKShopEngine::SLUG . '_form', 
            'description' => sprintf(__('Используйте этот виджет, чтобы добавить форму входа/корзины Вашего магазина или вставьте код %s в любом весте вашего шаблона.', $AVKShopEngine::SLUG), '<?php global $AVKShopEngine; $AVKShopEngine->HTML->user_login_form(true); ?>'));
        parent::__construct('id-' . $AVKShopEngine::SLUG, sprintf(__('Корзина %s', $AVKShopEngine::SLUG), $AVKShopEngine->name), $widget_ops);
        $this->alt_option_name = 'widget_' . $AVKShopEngine::SLUG . '_form';
	}

	public function widget( $args, $instance ) {
        global $AVKShopEngine;
		echo $AVKShopEngine->widget($args, $instance);
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? addcslashes(htmlspecialchars(trim(strip_tags($new_instance['title'])),ENT_QUOTES),"`") : '';

		return $instance;
	}

	public function form( $instance ) {
        global $AVKShopEngine;
		
        $title = ! empty( $instance['title'] ) ? $instance['title'] : null;
        $label = $AVKShopEngine->LIB->html( 'label', array( 'for' => $this->get_field_id( 'title' ) ), __('Title:') );
        $input = $AVKShopEngine->LIB->html( 'input', array( 'id' => $this->get_field_id( 'title' ),
                                                       'class' => 'widefat',
                                                       'name' => $this->get_field_name( 'title' ),
                                                       'type' => 'text',
                                                       'value' => esc_attr( $title )) );
        echo $AVKShopEngine->LIB->html( 'p', $label . $input);
	}
}