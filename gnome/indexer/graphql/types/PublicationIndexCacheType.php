<?php
namespace gnome\indexer\graphql\types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PublicationIndexCacheType {
    private static $type;
    public static function type() {
        if ( !self::$type ) {
            self::$type = new ObjectType( [
                'name' => 'PublicationIndexCache',
                'fields' => [
                    'publication_id' => [
                        'type' => Type::nonNull( Type::string() ),
                        'description' => 'The ID of the publication'
                    ],
                    'indices_id' => [
                        'type' => Type::nonNull( Type::int() ),
                        'description' => 'The ID of the index'
                    ],
                    'track_value_json' => [
                        'type' => Type::string(),
                        'description' => 'JSON data of the track values'
                    ]
                ]
            ] );
        }

        return self::$type;
    }
}
