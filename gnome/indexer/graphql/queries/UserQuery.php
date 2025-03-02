<?php 
namespace gnome\indexer\graphql\queries;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use gnome\indexer\graphql\types\UserType;
// userModel
use gnome\classes\model\User;

class UserQuery {

    public static function getAllUsers() {
        return [
            'type' => Type::listOf(UserType::type()),
            'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                // Logic to fetch and return all users
                $userModel = new User();
                return $userModel->getUsers();
            }
        ];
    }

    public static function getUserById() {
        return [
            'type' => UserType::type(),
            'args' => [
                'user_id' => Type::nonNull(Type::int())
            ],
            'resolve' => function ($root, $args, $context, ResolveInfo $info) {
                // Logic to fetch a user by ID
                $id = $args['user_id'];
                $userModel = new User();
                return $userModel->getUserById($id);
            }
        ];
    }
}