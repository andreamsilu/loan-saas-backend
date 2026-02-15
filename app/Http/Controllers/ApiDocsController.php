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
                'title' => 'Loan SaaS API',
                'version' => '1.0.0',
                'description' => 'Multi-tenant loan SaaS API grouped by module',
            ],
            'servers' => [
                [
                    'url' => rtrim(config('app.url'), '/') . '/api',
                ],
            ],
            'tags' => [
                ['name' => 'User'],
                ['name' => 'Tenant'],
                ['name' => 'Borrower'],
                ['name' => 'Loan'],
                ['name' => 'Transaction'],
                ['name' => 'Payment'],
                ['name' => 'Billing'],
                ['name' => 'Report'],
                ['name' => 'Owner'],
                ['name' => 'Developer'],
                [
                    'name' => 'Notification',
                    'description' => 'Outbound notifications via SMS (NextSMS) and webhooks for loan events',
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
                '/tenant/subscription/current' => [
                    'get' => [
                        'tags' => ['Tenant'],
                        'summary' => 'Get current tenant subscription info',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Subscription info'],
                            '404' => ['description' => 'Not found'],
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
                '/tenant/subscription/billing-cycle' => [
                    'post' => [
                        'tags' => ['Tenant'],
                        'summary' => 'Change tenant billing cycle',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Updated'],
                        ],
                    ],
                ],
                '/tenant/subscription/history' => [
                    'get' => [
                        'tags' => ['Tenant'],
                        'summary' => 'Get tenant subscription invoices history',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'List of invoices'],
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
                '/billing/dashboard' => [
                    'get' => [
                        'tags' => ['Billing'],
                        'summary' => 'Tenant billing dashboard',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Dashboard metrics'],
                        ],
                    ],
                ],
                '/billing/invoices' => [
                    'get' => [
                        'tags' => ['Billing'],
                        'summary' => 'List invoices',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'List of invoices'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Billing'],
                        'summary' => 'Create invoice',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '201' => ['description' => 'Invoice created'],
                        ],
                    ],
                ],
                '/billing/invoices/{id}' => [
                    'get' => [
                        'tags' => ['Billing'],
                        'summary' => 'Get invoice',
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
                            '200' => ['description' => 'Invoice'],
                            '404' => ['description' => 'Not found'],
                        ],
                    ],
                ],
                '/billing/invoices/{id}/mark-paid' => [
                    'post' => [
                        'tags' => ['Billing'],
                        'summary' => 'Mark invoice as paid',
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
                            '200' => ['description' => 'Invoice updated'],
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
                '/owner/tenants' => [
                    'get' => [
                        'tags' => ['Owner'],
                        'summary' => 'List all tenants',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'List of tenants'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Owner'],
                        'summary' => 'Create tenant and admin',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '201' => ['description' => 'Tenant created'],
                        ],
                    ],
                ],
                '/owner/tenants/{id}/plan' => [
                    'post' => [
                        'tags' => ['Owner'],
                        'summary' => 'Set tenant subscription plan',
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
                            '201' => ['description' => 'Subscription created'],
                        ],
                    ],
                ],
                '/owner/tenants/{id}/suspend' => [
                    'post' => [
                        'tags' => ['Owner'],
                        'summary' => 'Suspend tenant',
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
                            '200' => ['description' => 'Suspended'],
                        ],
                    ],
                ],
                '/owner/tenants/{id}/activate' => [
                    'post' => [
                        'tags' => ['Owner'],
                        'summary' => 'Activate tenant',
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
                            '200' => ['description' => 'Activated'],
                        ],
                    ],
                ],
                '/owner/tenants/{id}/billing-cycle' => [
                    'post' => [
                        'tags' => ['Owner'],
                        'summary' => 'Update tenant billing cycle',
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
                '/owner/tenants/{id}/reset-credentials' => [
                    'post' => [
                        'tags' => ['Owner'],
                        'summary' => 'Reset tenant admin password',
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
                            '200' => ['description' => 'Password reset'],
                        ],
                    ],
                ],
                '/owner/analytics/dashboard' => [
                    'get' => [
                        'tags' => ['Owner'],
                        'summary' => 'Owner analytics dashboard',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Analytics'],
                        ],
                    ],
                ],
                '/owner/settings' => [
                    'get' => [
                        'tags' => ['Owner'],
                        'summary' => 'Get global settings',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Settings'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Owner'],
                        'summary' => 'Update global settings',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Settings updated'],
                        ],
                    ],
                ],
                '/developer/keys' => [
                    'get' => [
                        'tags' => ['Developer'],
                        'summary' => 'List API keys',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Keys'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Developer'],
                        'summary' => 'Create API key',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '201' => ['description' => 'Key created'],
                        ],
                    ],
                ],
                '/developer/keys/{id}/rotate' => [
                    'post' => [
                        'tags' => ['Developer'],
                        'summary' => 'Rotate API key',
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
                            '200' => ['description' => 'Rotated'],
                        ],
                    ],
                ],
                '/developer/keys/{id}/revoke' => [
                    'post' => [
                        'tags' => ['Developer'],
                        'summary' => 'Revoke API key',
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
                            '200' => ['description' => 'Revoked'],
                        ],
                    ],
                ],
                '/developer/usage' => [
                    'get' => [
                        'tags' => ['Developer'],
                        'summary' => 'Get recent API usage logs',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Usage logs'],
                        ],
                    ],
                ],
                '/developer/webhooks' => [
                    'get' => [
                        'tags' => ['Developer', 'Notification'],
                        'summary' => 'List webhook endpoints',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => ['description' => 'Webhooks'],
                        ],
                    ],
                    'post' => [
                        'tags' => ['Developer', 'Notification'],
                        'summary' => 'Create webhook endpoint',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '201' => ['description' => 'Webhook created'],
                        ],
                    ],
                ],
                '/developer/webhooks/{id}' => [
                    'put' => [
                        'tags' => ['Developer', 'Notification'],
                        'summary' => 'Update webhook endpoint',
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
                '/developer/webhooks/{id}/logs' => [
                    'get' => [
                        'tags' => ['Developer', 'Notification'],
                        'summary' => 'Get webhook logs',
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
                            '200' => ['description' => 'Logs'],
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

        return response(
            '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Loan SaaS API Docs</title><link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css"></head><body><div id="swagger-ui"></div><script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script><script>window.onload=function(){SwaggerUIBundle({url:"' . $specUrl . '",dom_id:"#swagger-ui"});}</script></body></html>',
            200,
            ['Content-Type' => 'text/html']
        );
    }
}
