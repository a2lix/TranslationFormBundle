UPGRADE FROM 1.1 to 1.2
=======================

### [BC Break] Rename of 'type' option into 'field_type'



Previous code:

    $builder->add('translations', 'a2lix_translations', array(
        'fields' => array(
            'description' => array(
                'type' => 'textarea',
            )
        )
    ))


Change into:

    $builder->add('translations', 'a2lix_translations', array(
        'fields' => array(
            'description' => array(
                'field_type' => 'textarea',
            )
        )
    ))


This modification occurs for manage collection, which need type field (http://symfony.com/doc/current/reference/forms/types/collection.html#type):

    $builder->add('translations', 'a2lix_translations', array(
        'fields' => array(
            'textCollection' => array(
                'field_type' => 'collection',
                'type' => 'text',
                'allow_add' => true,
                'allow_remove' => false
            )
        )
    ))
