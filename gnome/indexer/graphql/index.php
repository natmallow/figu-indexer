<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('GRAPHQL_DEBUG', true);

use GraphQL\GraphQL;
use GraphQL\Error\DebugFlag;
use gnome\indexer\graphql\AppSchema;

header('Content-Type: application/json');

try {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    $query = $input['query'] ?? '';
    $variables = $input['variables'] ?? null;

    $schema = AppSchema::get();

    $debugFlag = GRAPHQL_DEBUG ? (DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE) : 0;
    $result = GraphQL::executeQuery($schema, $query, null, null, $variables)
        ->setErrorsHandler(function ($errors, callable $formatter) use ($debugFlag) {
            return array_map(function ($error) use ($formatter, $debugFlag) {
                return $formatter($error, $debugFlag);
            }, $errors);
        })
        ->toArray($debugFlag);

} catch (\Throwable $e) { // \Throwable catches both Error and Exception
    $error = ['message' => $e->getMessage()];
    if (GRAPHQL_DEBUG) {
        $error += [
            'trace' => $e->getTraceAsString(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
    $result = ['errors' => [$error]];
}

echo json_encode($result);
