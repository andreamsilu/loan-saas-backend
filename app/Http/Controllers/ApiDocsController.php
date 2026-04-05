<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ApiDocsController extends Controller
{
    public function openApi(): JsonResponse
    {
        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Loan Management API',
                'version' => '1.0.0',
                'description' => 'REST API for borrowers, loan products, loans, repayments, and operational reporting. '
                    . 'Scope requests with the `X-Tenant-ID` header or tenant host/domain resolution.',
            ],
            'servers' => [
                [
                    'url' => url('/api'),
                ],
            ],
            'tags' => [
                ['name' => 'User'],
                ['name' => 'RolePermission'],
                ['name' => 'Tenant'],
                ['name' => 'Borrower'],
                ['name' => 'Loan'],
                ['name' => 'Payment'],
                ['name' => 'Report'],
                [
                    'name' => 'Notification',
                    'description' => 'Outbound SMS and email for loan lifecycle events',
                ],
            ],
            'paths' => [
                '/user/login' => [
                    'post' => [
                        'tags' => ['User'],
                        'summary' => 'Login user',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'login' => ['type' => 'string'],
                                            'password' => ['type' => 'string'],
                                            'device_name' => ['type' => 'string'],
                                        ],
                                        'required' => ['login', 'password', 'device_name'],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Successful login',
                            ],
                            '422' => [
                                'description' => 'Validation error',
                            ],
                        ],
                    ],
                ],
                '/user/register' => [
                    'post' => [
                        'tags' => ['User'],
                        'summary' => 'Register user',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'name' => ['type' => 'string'],
                                            'email' => ['type' => 'string'],
                                            'phone' => ['type' => 'string'],
                                            'password' => ['type' => 'string'],
                                            'password_confirmation' => ['type' => 'string'],
                                            'device_name' => ['type' => 'string'],
                                        ],
                                        'required' => ['name', 'email', 'password', 'password_confirmation', 'device_name'],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'User registered',
                            ],
                            '422' => [
                                'description' => 'Validation error',
                            ],
                        ],
                    ],
                ],
                '/user/logout' => [
                    'post' => [
                        'tags' => ['User'],
                        'summary' => 'Logout user',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Logged out'],
                        ],
                    ],
                ],
                '/user/me' => [
                    'get' => [
                        'tags' => ['User'],
                        'summary' => 'Get current user',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Current user'],
                        ],
                    ],
                ],
                '/user/staff' => [
                    'get' => [
                        'tags' => ['User'],
                        'summary' => 'List staff and tenant admins for current tenant',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'List of staff'],
                        ],
                    ],
                ],
                '/user/staff/{id}/role' => [
                    'post' => [
                        'tags' => ['User'],
                        'summary' => 'Change staff role within tenant',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Updated'],
                            '403' => ['description' => 'Forbidden'],
                        ],
                    ],
                ],
                '/user/role-permissions' => [
                    'get' => [
                        'tags' => ['RolePermission'],
                        'summary' => 'List role-permissions for current tenant',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'List of role permissions'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['RolePermission'],
                        'summary' => 'Assign permission to role for current tenant',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '201' => ['description' => 'Created'],
                        ],
                    ],
                ],
                '/user/role-permissions/{id}' => [
                    'delete' => [
                        'tags' => ['RolePermission'],
                        'summary' => 'Remove permission from role for current tenant',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Deleted'],
                            '403' => ['description' => 'Forbidden'],
                        ],
                    ],
                ],
                '/user/{id}/roles' => [
                    'get' => [
                        'tags' => ['RolePermission'],
                        'summary' => 'List additional roles assigned to user within tenant',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'List of roles'],
                            '403' => ['description' => 'Forbidden'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['RolePermission'],
                        'summary' => 'Assign additional role to user within tenant',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '201' => ['description' => 'Created'],
                            '403' => ['description' => 'Forbidden'],
                        ],
                    ],
                ],
                '/user/{id}/roles/{role}' => [
                    'delete' => [
                        'tags' => ['RolePermission'],
                        'summary' => 'Remove additional role from user within tenant',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                            [
                                'name' => 'role',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'string'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Deleted'],
                            '403' => ['description' => 'Forbidden'],
                        ],
                    ],
                ],
                '/tenant/me' => [
                    'get' => [
                        'tags' => ['Tenant'],
                        'summary' => 'Get current tenant from context',
                        'responses' => [
                            '200' => ['description' => 'Tenant info'],
                            '404' => ['description' => 'Tenant not resolved'],
                        ],
                    ],
                ],
                '/tenant/settings/branding' => [
                    'post' => [
                        'tags' => ['Tenant'],
                        'summary' => 'Update tenant branding settings',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Updated'],
                        ],
                    ],
                ],
                '/tenant/settings/ui-flags' => [
                    'post' => [
                        'tags' => ['Tenant'],
                        'summary' => 'Update tenant UI configuration flags',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Updated'],
                        ],
                    ],
                ],
                '/tenant/settings/domain' => [
                    'post' => [
                        'tags' => ['Tenant'],
                        'summary' => 'Update tenant domain and subdomain',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Updated'],
                        ],
                    ],
                ],
                '/tenant/settings/sms' => [
                    'post' => [
                        'tags' => ['Tenant'],
                        'summary' => 'Update tenant SMS provider configuration',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Updated'],
                        ],
                    ],
                ],
                '/borrower' => [
                    'get' => [
                        'tags' => ['Borrower'],
                        'summary' => 'List borrowers',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'List of borrowers'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Borrower'],
                        'summary' => 'Create borrower',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '201' => ['description' => 'Borrower created'],
                        ],
                    ],
                ],
                '/borrower/{id}' => [
                    'get' => [
                        'tags' => ['Borrower'],
                        'summary' => 'Get borrower',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Borrower'],
                            '404' => ['description' => 'Not found'],
                        ],
                    ],
                    'put' => [
                        'tags' => ['Borrower'],
                        'summary' => 'Update borrower',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Updated'],
                        ],
                    ],
                ],
                '/borrower/{id}/blacklist' => [
                    'post' => [
                        'tags' => ['Borrower'],
                        'summary' => 'Blacklist borrower',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Blacklisted'],
                        ],
                    ],
                ],
                '/loan/products' => [
                    'get' => [
                        'tags' => ['Loan'],
                        'summary' => 'List loan products',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'List of products'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Loan'],
                        'summary' => 'Create loan product',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '201' => ['description' => 'Product created'],
                        ],
                    ],
                ],
                '/loan/products/{id}' => [
                    'get' => [
                        'tags' => ['Loan'],
                        'summary' => 'Get loan product',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Loan product'],
                            '404' => ['description' => 'Not found'],
                        ],
                    ],
                    'put' => [
                        'tags' => ['Loan'],
                        'summary' => 'Update loan product',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Updated'],
                        ],
                    ],
                    'delete' => [
                        'tags' => ['Loan'],
                        'summary' => 'Delete loan product',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Deleted'],
                            '404' => ['description' => 'Not found'],
                        ],
                    ],
                ],
                '/loan/loans' => [
                    'post' => [
                        'tags' => ['Loan'],
                        'summary' => 'Create loan',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '201' => ['description' => 'Loan created'],
                        ],
                    ],
                ],
                '/loan/loans/{id}' => [
                    'get' => [
                        'tags' => ['Loan'],
                        'summary' => 'Get loan',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Loan'],
                            '404' => ['description' => 'Not found'],
                        ],
                    ],
                ],
                '/loan/loans/{id}/repay' => [
                    'post' => [
                        'tags' => ['Loan'],
                        'summary' => 'Create loan repayment',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Repayment recorded'],
                        ],
                    ],
                ],
                '/loan/loans/{id}/approve' => [
                    'post' => [
                        'tags' => ['Loan'],
                        'summary' => 'Approve loan',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Approved'],
                        ],
                    ],
                ],
                '/loan/loans/{id}/disburse' => [
                    'post' => [
                        'tags' => ['Loan'],
                        'summary' => 'Disburse loan',
                        'security' => [['sanctum' => []]],
                        'parameters' => [
                            [
                                'name' => 'id',
                                'in' => 'path',
                                'required' => true,
                                'schema' => ['type' => 'integer'],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Disbursed'],
                        ],
                    ],
                ],
                '/report/dashboard' => [
                    'get' => [
                        'tags' => ['Report'],
                        'summary' => 'Tenant operational dashboard',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Dashboard'],
                        ],
                    ],
                ],
                '/report/disbursement-trends' => [
                    'get' => [
                        'tags' => ['Report'],
                        'summary' => 'Disbursement trends',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Trends'],
                        ],
                    ],
                ],
                '/payment/aggregator/ipn' => [
                    'get' => [
                        'tags' => ['Payment'],
                        'summary' => 'Aggregator IPN callback (GET)',
                        'responses' => [
                            '200' => ['description' => 'Handled'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Payment'],
                        'summary' => 'Aggregator IPN callback (POST)',
                        'responses' => [
                            '200' => ['description' => 'Handled'],
                        ],
                    ],
                ],
            ],
            'components' => [
                'securitySchemes' => [
                    'sanctum' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'Token',
                    ],
                ],
            ],
        ];

        return response()->json($spec);
    }

    public function ui()
    {
        $specUrl = url('/docs/openapi.json');
        $docTitle = 'Loan Management API Docs';

        return response(
            '<!DOCTYPE html><html><head><meta charset="utf-8"><title>'
            . htmlspecialchars($docTitle, ENT_QUOTES, 'UTF-8')
            . '</title><link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css"></head><body><div id="swagger-ui"></div><script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script><script>window.onload=function(){SwaggerUIBundle({url:"'
            . $specUrl
            . '",dom_id:"#swagger-ui"});}</script></body></html>',
            200,
            ['Content-Type' => 'text/html']
        );
    }
}
