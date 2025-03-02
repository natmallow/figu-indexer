<?php

namespace gnome\indexer\graphql;

use gnome\classes\model\Indices;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use gnome\indexer\graphql\types\UserType;
use gnome\indexer\graphql\queries\UserQuery;
use gnome\indexer\graphql\queries\PublicationIndexCacheQuery;
use gnome\indexer\graphql\mutations\UserMutation;
use gnome\indexer\graphql\queries\AppQuery;
use gnome\indexer\graphql\types\PublicationIndexCacheType;
use gnome\indexer\graphql\types\IndicesType;

class AppSchema {
    public static function get()
    {
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'users' => UserQuery::getAllUsers(),
                'user' => UserQuery::getUserById(),
                'indices' => AppQuery::getIndices(),
                'searchIndices' => AppQuery::searchIndicesByKeyword(),
                'publicationIndexCache' => PublicationIndexCacheQuery::getPublicationIndexCache()
                // 'post' => PostQuery::get(),
                // ... other queries
            ]
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                // 'createUser' => CreateUserMutation::get(),
                // ... other mutations
            ]
        ]);

        return new Schema([
            'query' => $queryType,
            'mutation' => $mutationType,
            // If you use types that are not directly used in queries or mutations, 
            // you might need to declare them here as well
            'types' => [
                UserType::type(),
                PublicationIndexCacheType::type(),
                IndicesType::type(),
                // Other types as needed
            ],
            'typeLoader' => function ($name) {
                // A function that returns an instance of each type by its name
                switch ($name) {
                    case 'User':
                        return UserType::type();
                    case 'PublicationIndexCache':
                        return PublicationIndexCacheType::type();            
                    case 'Indices':
                        return IndicesType::type();                   
                    case 'Post':
                        // return PostType::get();
                        // ... other types
                }
            }
        ]);
    }
}
