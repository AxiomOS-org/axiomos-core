<?php
declare(strict_types=1);
namespace Tests\Feature\Modules\Reporting;
use App\Core\Http\HttpKernelFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\Stability\KernelTestHarness;
final class ReportingPlatformTest extends KernelTestHarness {
    public function test_module_routes_do_not_return_500(): void {
        $company = $this->decodeJson((string) $this->kernel->handle(Request::create('/api/companies?page=1&per_page=1', 'GET'))->getContent());
        $companyId = (string) ($company['data'][0]['id'] ?? '');
        $failures = [];
        foreach ([
            '/api/reporting/report-definition?company_id=' . $companyId,
            '/api/reporting/report-snapshot?company_id=' . $companyId,
            '/reporting',
            '/reporting/dashboard',
        ] as $path) {
            $response = $this->kernel->handle(Request::create($path, 'GET'));
            if ($response->getStatusCode() >= 500) {
                $failures[] = $path . ' => ' . $response->getStatusCode();
            }
        }
        self::assertSame([], $failures, implode("\n", $failures));
    }
}