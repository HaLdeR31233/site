<?php

/**
 * Контролер для API роботи з нерухомістю через MyModel
 *
 * Демонструє використання моделі MyModel для всіх операцій з нерухомістю
 */

namespace App\Classes;

use Monolog\Logger;
use Symfony\Component\VarDumper\VarDumper;

class PropertyApiController
{
    private MyModel $model;
    private Logger $logger;

    public function __construct(MyModel $model, Logger $logger)
    {
        $this->model = $model;
        $this->logger = $logger;
    }

    /**
     * Отримати всі об'єкти нерухомості
     */
    public function index(): void
    {
        try {
            $filters = $_GET;
            $limit = (int) ($_GET['limit'] ?? 20);
            $offset = (int) ($_GET['offset'] ?? 0);

            $properties = $this->model->getProperties($filters, $limit, $offset);
            $stats = $this->model->getStatistics();

            $response = [
                'success' => true,
                'data' => [
                    'properties' => array_map(function($property) {
                        return $property->toArray();
                    }, $properties),
                    'statistics' => $stats,
                    'pagination' => [
                        'limit' => $limit,
                        'offset' => $offset,
                        'total' => $stats['total'] ?? 0
                    ]
                ]
            ];

            $this->jsonResponse($response);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Отримати конкретний об'єкт нерухомості
     */
    public function show(int $id): void
    {
        try {
            $property = $this->model->getProperty($id);

            if (!$property) {
                $this->jsonResponse(['success' => false, 'error' => 'Property not found'], 404);
                return;
            }

            $response = [
                'success' => true,
                'data' => $property->toArray()
            ];

            $this->jsonResponse($response);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Створити новий об'єкт нерухомості
     */
    public function store(): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid JSON data'], 400);
                return;
            }

            $property = $this->model->createProperty($data);

            $response = [
                'success' => true,
                'data' => $property->toArray(),
                'message' => 'Property created successfully'
            ];

            $this->jsonResponse($response, 201);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Оновити об'єкт нерухомості
     */
    public function update(int $id): void
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid JSON data'], 400);
                return;
            }

            $property = $this->model->updateProperty($id, $data);

            $response = [
                'success' => true,
                'data' => $property->toArray(),
                'message' => 'Property updated successfully'
            ];

            $this->jsonResponse($response);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Видалити об'єкт нерухомості
     */
    public function delete(int $id): void
    {
        try {
            $result = $this->model->deleteProperty($id);

            if ($result) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Property deleted successfully'
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to delete property'], 500);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Пошук об'єктів нерухомості
     */
    public function search(): void
    {
        try {
            $query = $_GET['q'] ?? '';
            $filters = $_GET;
            unset($filters['q']);

            if (empty($query)) {
                $this->jsonResponse(['success' => false, 'error' => 'Search query is required'], 400);
                return;
            }

            $results = $this->model->searchProperties($query, $filters);

            $response = [
                'success' => true,
                'data' => [
                    'query' => $query,
                    'results' => array_map(function($property) {
                        return $property->toArray();
                    }, $results),
                    'count' => count($results)
                ]
            ];

            $this->jsonResponse($response);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Отримати статистику
     */
    public function statistics(): void
    {
        try {
            $stats = $this->model->getStatistics();

            $response = [
                'success' => true,
                'data' => $stats
            ];

            $this->jsonResponse($response);
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Згенерувати звіт
     */
    public function report(): void
    {
        try {
            $format = $_GET['format'] ?? 'json';

            $report = $this->model->generateReport($format);

            if ($format === 'json') {
                header('Content-Type: application/json');
                echo $report;
            } elseif ($format === 'csv') {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="properties_report.csv"');
                echo $report;
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Unsupported format'], 400);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Відправити JSON відповідь
     */
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
