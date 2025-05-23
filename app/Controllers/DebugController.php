<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class DebugController extends BaseController
{
    use ResponseTrait;

    public function testSession()
    {
        // Only available in development
        if (ENVIRONMENT !== 'development') {
            return $this->failUnauthorized('Not available in production');
        }

        // Set a test value
        session()->set('test_value', 'Session is working at ' . date('Y-m-d H:i:s'));

        // Return current session data
        return $this->respond([
            'session_id' => session_id(),
            'session_data' => session()->get(),
            'cookies' => $_COOKIE,
        ]);
    }

    public function testDatabase()
    {
        // Only available in development
        if (ENVIRONMENT !== 'development') {
            return $this->failUnauthorized('Not available in production');
        }

        $db = \Config\Database::connect();

        try {
            $tables = $db->listTables();
            $userTable = $db->table('users')->get()->getResultArray();

            return $this->respond([
                'connected' => true,
                'tables' => $tables,
                'user_table_exists' => in_array('users', $tables),
                'user_count' => count($userTable),
                'first_few_users' => array_slice($userTable, 0, 5),
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'connected' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
