<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\Client as MongoDBClient;

class SecretQuestionController extends Controller
{
    /**
     * Get all secret questions from MongoDB.
     */
    public function index(Request $request)
    {
        try {
            $client = new MongoDBClient(env('MONGODB_URI'));
            $database = $client->selectDatabase('equipo');
            $collection = $database->selectCollection('recuperar-password');

            $cursor = $collection->find();
            $preguntas = [];

            foreach ($cursor as $document) {
                $preguntas[] = iterator_to_array($document);
            }

            return response()->json([
                'preguntas' => $preguntas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'preguntas' => [],
                'message' => 'Error al cargar preguntas secretas.',
            ], 500);
        }
    }
}

