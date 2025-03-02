<?php
namespace gnome\indexer\graphql\types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class UserType {
    private static $type;

    public static function type() {
        if (!self::$type) {
            self::$type = new ObjectType([
                'name' => 'User',
                'fields' => [
                    'user_id' => [
                        'type' => Type::nonNull(Type::int()),
                        'description' => 'The unique id of the user'
                    ],
                    'username' => [
                        'type' => Type::string(),
                        'description' => 'Username'
                    ],
                    'email' => [
                        'type' => Type::string(),
                        'description' => 'Email address'
                    ],
                    'password' => [
                        'type' => Type::string(), // Even though it's a blob in the database, it's represented as a string in GraphQL
                        'description' => 'User password'
                    ],
                    'create_time' => [
                        'type' => Type::string(), // Timestamps can be represented as strings
                        'description' => 'The time the user account was created'
                    ],
                    'name_first' => [
                        'type' => Type::string(),
                        'description' => 'First name'
                    ],
                    'name_last' => [
                        'type' => Type::string(),
                        'description' => 'Last name'
                    ],
                    'phone' => [
                        'type' => Type::string(),
                        'description' => 'Phone number'
                    ],
                    'attempts' => [
                        'type' => Type::int(),
                        'description' => 'Number of login attempts'
                    ],
                    'is_locked' => [
                        'type' => Type::boolean(),
                        'description' => 'Is the account locked?'
                    ],
                    'is_reset_password' => [
                        'type' => Type::boolean(),
                        'description' => 'Is password reset required?'
                    ],
                    'security_key' => [
                        'type' => Type::string(),
                        'description' => 'Security key for the user'
                    ],
                    'is_activated' => [
                        'type' => Type::boolean(),
                        'description' => 'Is the account activated?'
                    ],
                    'reason' => [
                        'type' => Type::string(),
                        'description' => 'Reason for account status'
                    ]
                ]
            
            ]);
        }

        return self::$type;
    }
}
