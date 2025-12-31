<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    /**
     * Hardcoded users untuk login
     */
    private $users = [
        'admin' => [
            'username' => 'admin',
            'password' => 'admin123', // In production, use password_hash()
            'data' => [
                'ID' => 'PTG001',
                'UID' => '16156151515',
                'Nama' => 'Admin',
                'Organisasi' => [
                    'ID' => 'ORG001',
                    'Nama' => 'Software Development'
                ],
                'HakAkses' => [
                    'reporting' => [
                        [
                            'AdminReporting' => true
                        ],
                        [
                            'Reporting' => true
                        ]
                    ]
                ]
            ]
        ],
        'user' => [
            'username' => 'user',
            'password' => 'user123', // In production, use password_hash()
            'data' => [
                'ID' => 'PTG002',
                'UID' => '16156151516',
                'Nama' => 'User Regular',
                'Organisasi' => [
                    'ID' => 'ORG002',
                    'Nama' => 'Quality Assurance'
                ],
                'HakAkses' => [
                    'reporting' => [
                        [
                            'AdminReporting' => false
                        ],
                        [
                            'Reporting' => true
                        ]
                    ]
                ]
            ]
        ]
    ];

    /**
     * Authenticate user by username and password
     * 
     * @param string $username
     * @param string $password
     * @return array|null User data if success, null if failed
     */
    public function authenticate(string $username, string $password): ?array
    {
        // Check if user exists
        if (!isset($this->users[$username])) {
            return null;
        }

        $user = $this->users[$username];

        // Verify password
        if ($user['password'] !== $password) {
            return null;
        }

        // Return user data (without password)
        return $user['data'];
    }

    /**
     * Get user by ID
     * 
     * @param string $userId
     * @return array|null
     */
    public function getUserById(string $userId): ?array
    {
        foreach ($this->users as $user) {
            if ($user['data']['ID'] === $userId) {
                return $user['data'];
            }
        }
        return null;
    }
}
