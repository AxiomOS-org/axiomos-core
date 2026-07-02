<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Organization;

use App\Core\Http\HttpKernel;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Tests\Support\PostgresFeatureTestCase;

/**
 * REST API tests for the Organization foundation hierarchy.
 */
final class OrganizationApiTest extends PostgresFeatureTestCase
{
    private HttpKernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kernel = HttpKernelFactory::create($this->basePath);
    }

    public function test_organizations_api_supports_pagination(): void
    {
        $response = $this->kernel->handle(Request::create('/api/organizations?page=1&per_page=5', 'GET'));

        self::assertSame(200, $response->getStatusCode());

        $payload = $this->decode($response->getContent());
        self::assertArrayHasKey('meta', $payload);
        self::assertSame(5, $payload['meta']['per_page']);
        self::assertGreaterThanOrEqual(10, $payload['meta']['total']);
    }

    public function test_it_creates_an_organization_via_api(): void
    {
        $response = $this->kernel->handle(Request::create('/api/organizations', 'POST', [
            'code' => 'NEWCO',
            'name' => 'New Company Org',
            'country' => 'US',
            'timezone' => 'America/New_York',
        ]));

        self::assertSame(201, $response->getStatusCode());

        $payload = $this->decode($response->getContent());
        self::assertSame('NEWCO', $payload['data']['code']);
        self::assertSame('new-company-org-newco', $payload['data']['slug']);
    }

    public function test_companies_require_organization_id(): void
    {
        $response = $this->kernel->handle(Request::create('/api/companies', 'POST', [
            'code' => 'SUB',
            'name' => 'Sub Company',
        ]));

        self::assertSame(422, $response->getStatusCode());
    }

    public function test_it_creates_company_branch_and_department_hierarchy(): void
    {
        $org = $this->decode($this->kernel->handle(Request::create('/api/organizations', 'GET'))->getContent())['data'][0];

        $companyResponse = $this->kernel->handle(Request::create('/api/companies', 'POST', [
            'organization_id' => $org['id'],
            'code' => 'SUB',
            'name' => 'Subsidiary',
        ]));
        self::assertSame(201, $companyResponse->getStatusCode());
        $company = $this->decode($companyResponse->getContent())['data'];

        $branchResponse = $this->kernel->handle(Request::create('/api/branches', 'POST', [
            'company_id' => $company['id'],
            'code' => 'BR1',
            'name' => 'Branch One',
        ]));
        self::assertSame(201, $branchResponse->getStatusCode());
        $branch = $this->decode($branchResponse->getContent())['data'];

        $departmentResponse = $this->kernel->handle(Request::create('/api/departments', 'POST', [
            'branch_id' => $branch['id'],
            'code' => 'HR',
            'name' => 'Human Resources',
        ]));
        self::assertSame(201, $departmentResponse->getStatusCode());

        $department = $this->decode($departmentResponse->getContent())['data'];
        self::assertSame($branch['id'], $department['branch_id']);
    }

    public function test_organization_browser_page_renders(): void
    {
        $response = $this->kernel->handle(Request::create('/organizations', 'GET'));

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Organizations', (string) $response->getContent());
        self::assertStringContainsString('bootstrap', (string) $response->getContent());
        self::assertStringContainsString('data-table', (string) $response->getContent());
    }

    public function test_companies_branches_and_departments_pages_render(): void
    {
        $expectations = [
            '/companies' => 'Companies',
            '/branches' => 'Branches',
            '/departments' => 'Departments',
        ];

        foreach ($expectations as $path => $title) {
            $response = $this->kernel->handle(Request::create($path, 'GET'));
            self::assertSame(200, $response->getStatusCode(), $path);
            self::assertStringContainsString($title, (string) $response->getContent(), $path);
            self::assertStringContainsString('data-table', (string) $response->getContent(), $path);
        }
    }

    public function test_demo_seeder_populates_hierarchy_counts(): void
    {
        $this->kernel->handle(Request::create('/api/organizations?page=1', 'GET'));

        self::assertGreaterThanOrEqual(10, \Modules\Organization\Domain\Models\Organization::query()->count());
        self::assertGreaterThanOrEqual(50, \Modules\Organization\Domain\Models\Company::query()->count());
        self::assertGreaterThanOrEqual(200, \Modules\Organization\Domain\Models\Branch::query()->count());
        self::assertGreaterThanOrEqual(500, \Modules\Organization\Domain\Models\Department::query()->count());
    }

    /**
     * @return array<string, mixed>
     */
    private function decode(string|false $content): array
    {
        self::assertIsString($content);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
