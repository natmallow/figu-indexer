<?php 
namespace gnome\indexer\graphql\queries;

use gnome\classes\model\PublicationIndexCache;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use gnome\indexer\graphql\types\PublicationIndexCacheType;



class PublicationIndexCacheQuery {
    public static function getPublicationIndexCache() {
        return [
            'type' => PublicationIndexCacheType::type(), // Changed to a single object type
            'args' => [
                'publication_id' => Type::nonNull(Type::string()),
                'indices_id' => Type::nonNull(Type::int())
            ],
            'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                $dataService = new PublicationIndexCache();
                return $dataService->getPublicationIndexCache($args['indices_id'], $args['publication_id']);
            }
        ];
    }
}
