<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\Client as MongoDBClient;
use Illuminate\Support\Facades\Log;

class SecretQuestionController extends Controller
{
    /**
     * Get all secret questions from MongoDB.
     */
    public function index(Request $request)
    {
        try {
            // Obtener la URI de MongoDB desde la configuración de Laravel
            $mongodbUri = config('database.connections.mongodb.dsn') ?: env('MONGODB_URI');
            $databaseName = config('database.connections.mongodb.database') ?: env('MONGODB_DATABASE', 'equipo');

            if (!$mongodbUri) {
                Log::error('MONGODB_URI no está configurada');
                return response()->json([
                    'preguntas' => [],
                    'message' => 'Error de configuración: MONGODB_URI no está definida.',
                ], 500);
            }

            $client = new MongoDBClient($mongodbUri);
            $database = $client->selectDatabase($databaseName);
            $collection = $database->selectCollection('recuperar-password');

            $cursor = $collection->find();
            $preguntas = [];

            foreach ($cursor as $document) {
                // Convertir el documento MongoDB a array
                $docArray = iterator_to_array($document);
                
                // Limpiar el documento para devolver solo los campos necesarios
                $pregunta = [
                    '_id' => (string) $docArray['_id'],
                    'pregunta' => $docArray['pregunta'] ?? '',
                ];
                
                $preguntas[] = $pregunta;
            }

            return response()->json([
                'preguntas' => $preguntas,
                'total' => count($preguntas),
            ], 200);
        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            Log::error('Error de conexión a MongoDB: ' . $e->getMessage());
            return response()->json([
                'preguntas' => [],
                'message' => 'Error de conexión a la base de datos.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error al cargar preguntas secretas: ' . $e->getMessage());
            return response()->json([
                'preguntas' => [],
                'message' => 'Error al cargar preguntas secretas.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

