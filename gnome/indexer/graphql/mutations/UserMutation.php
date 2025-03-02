<?php

namespace gnome\indexer\graphql\mutations;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use gnome\indexer\graphql\types\UserType;
use GraphQL;

// userModel
use gnome\classes\service\UserService;

class UserMutation {
    public static function get() {
        return [
            'type' => UserType::type(),
            'args' => [
                'username' => Type::nonNull(Type::string()),
                'email' => Type::nonNull(Type::string()),
                'password' => Type::nonNull(Type::string()),
                // Add other necessary fields here
            ],
            'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                $userService = new UserService();
                return $userService->createUser($args);
            }
        ];
    }
}