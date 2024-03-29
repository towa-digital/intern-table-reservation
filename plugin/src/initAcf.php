<?php
    if (function_exists('acf_add_local_field_group')):

        acf_add_local_field_group([
            'key' => 'group_5d497f3c590da',
            'title' => 'Reservierungsdaten',
            'fields' => [
                [
                    'key' => 'field_5d497f42a6d61',
                    'label' => 'von?',
                    'name' => 'from',
                    'type' => 'date_time_picker',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'display_format' => 'm/d/Y H:i:s',
                    'return_format' => 'm/d/Y H:i:s',
                    'first_day' => 1,
                ],
                [
                    'key' => 'field_5d497f73a6d62',
                    'label' => 'bis?',
                    'name' => 'to',
                    'type' => 'date_time_picker',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'display_format' => 'm/d/Y H:i:s',
                    'return_format' => 'm/d/Y H:i:s',
                    'first_day' => 1,
                ],
                [
                    'key' => 'field_5d4a8c232af89',
                    'label' => 'Tische',
                    'name' => 'tables',
                    'type' => 'relationship',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'post_type' => [
                        0 => 'tables',
                    ],
                    'taxonomy' => '',
                    'filters' => '',
                    'elements' => '',
                    'min' => 1,
                    'max' => '',
                    'return_format' => 'id',
                ],
                [
                    'key' => 'field_5d4a8cdad75e0',
                    'label' => 'Vorname',
                    'name' => 'firstname',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5d4a8d0e154c3',
                    'label' => 'Nachname',
                    'name' => 'lastname',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5d4a8d1d154c4',
                    'label' => 'E-Mail',
                    'name' => 'mail',
                    'type' => 'email',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ],
                [
                    'key' => 'field_5d4a8d44154c5',
                    'label' => 'Telefonnummmer',
                    'name' => 'phonenumber',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5d4a8d6af434d',
                    'label' => 'IP-Adresse',
                    'name' => 'ip',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5d513bc84a83a',
                    'label' => 'Anzahl Sitzplätze',
                    'name' => 'numberOfSeats',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'min' => '',
                    'max' => '',
                    'step' => '',
                ],
                [
                    'key' => 'field_5d4a8cdad7577',
                    'label' => 'Anmerkungen',
                    'name' => 'remarks',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5d4a8d0e3425',
                    'label' => 'positionX',
                    'name' => 'posX',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5d4a8d0e15n5',
                    'label' => 'positionY',
                    'name' => 'posY',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5d4a8d0e30i8',
                    'label' => 'Höhe',
                    'name' => 'height',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],
                [
                    'key' => 'field_5d4a8d0e0234',
                    'label' => 'Breite',
                    'name' => 'width',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ]
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'reservations',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ]);
        
        acf_add_local_field_group([
            'key' => 'group_5d497e6e8010d',
            'title' => 'Tischdaten',
            'fields' => [
                [
                    'key' => 'field_5d497e7cb0cec',
                    'label' => 'außen?',
                    'name' => 'isOutside',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'message' => 'Ist der Tisch im Außenbereich?',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ],
                [
                    'key' => 'field_5d497e7cb0cee',
                    'label' => 'nicht reservierbar?',
                    'name' => 'isDisabled',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'message' => 'Ist der Tisch nicht reservierbar?',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ],
                [
                    'key' => 'field_5d497eb1b0ced',
                    'label' => 'Anzahl Sitzplätze',
                    'name' => 'seats',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => 1,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'min' => 1,
                    'max' => 999,
                    'step' => 1,
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'tables',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ]);

        acf_add_local_field_group(array(
            'key' => 'group_5d64fc5274b05',
            'title' => 'Objektdaten',
            'fields' => array(
                [
                    'key' => 'field_5d497e7cdeeec',
                    'label' => 'außen?',
                    'name' => 'isOutside',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'message' => 'Ist der Tisch im Außenbereich?',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ],
                array(
                    'key' => 'field_5d64fc60d5612',
                    'label' => 'startX',
                    'name' => 'startX',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'step' => '0.01',
                ),
                array(
                    'key' => 'field_5d64fc60d5615',
                    'label' => 'startY',
                    'name' => 'startY',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'step' => '0.01',
                ),
                array(
                    'key' => 'field_5d64fc67d5612',
                    'label' => 'endX',
                    'name' => 'endX',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'step' => '0.01',
                ),
                array(
                    'key' => 'field_5d64fc6075615',
                    'label' => 'endY',
                    'name' => 'endY',
                    'type' => 'number',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'step' => '0.01',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'objects',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));
        
        endif;
