<?php 
namespace gnome\indexer\graphql\queries;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;

use gnome\indexer\graphql\types\IndicesType;

// userModel
use gnome\classes\service\IndexerAppService;

class AppQuery {

    public static function getIndices() {
        return [
            'type' => Type::listOf(IndicesType::type()),
            'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                // Logic to fetch and return all users
                $IndexerAppService = new IndexerAppService();
                return $IndexerAppService->getAvailableIndices();
            }
        ];
    }

    public static function searchIndicesByKeyword() {
        return [
            'type' => Type::listOf(IndicesType::type()),
            'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                // Logic to fetch and return all users
                $IndexerAppService = new IndexerAppService();
                return $IndexerAppService->getAvailableIndices();
            }
        ];
    }

}