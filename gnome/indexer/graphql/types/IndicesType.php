<?php 
namespace gnome\indexer\graphql\types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class IndicesType {
    private static $type;

    public static function type() {
        if (!self::$type) {
            self::$type = new ObjectType([
                'name' => 'Indices',
                'fields' => [
                    'indices_id' => [
                        'type' => Type::nonNull(Type::int()),
                        'description' => 'The unique ID of the index'
                    ],
                    'name' => [
                        'type' => Type::string(),
                        'description' => 'Name of the index'
                    ],
                    'description_html' => [
                        'type' => Type::string(),
                        'description' => 'HTML description of the index'
                    ],
                    'highlight_color' => [
                        'type' => Type::string(),
                        'description' => 'Highlight color for the index'
                    ],
                    'text_color' => [
                        'type' => Type::string(),
                        'description' => 'Text color for the index'
                    ],
                    'created_by' => [
                        'type' => Type::string(),
                        'description' => 'Creator of the index'
                    ],
                    'created_at' => [
                        'type' => Type::string(),
                        'description' => 'Timestamp when the index was created'
                    ]
                ]
            ]);
        }

        return self::$type;
    }
}
