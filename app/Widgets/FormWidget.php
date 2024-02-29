<?php

namespace App\Widgets;

class FormWidget extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct('form-small', 'Form Small', [
            'description' => 'A small form.'
        ]);
    }
    public function widget($args, $instance) {
        echo '<h2>Custom Widget Content</h2>';
        echo '<p>En dan wat content</p>';
    }
    public function form( $instance ) {}
    public function update( $new_instance, $old_instance ) {}
}
