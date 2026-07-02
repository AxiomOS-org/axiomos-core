<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Identity\Application\Services\ContactService;
use Modules\Identity\Application\Support\ListQuery;
use Modules\Identity\Http\Requests\ContactRequestRules;
use Modules\Identity\Policies\ContactPolicy;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class ContactApiController extends ApiController
{
    public function __construct(
        private readonly ContactService $service,
        private readonly ContactPolicy $policy,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        if (! $this->policy->viewAny()) {
            return $this->forbidden();
        }

        if (ListQuery::wantsPagination($request)) {
            return $this->paginated($this->service->paginate(ListQuery::fromRequest($request)));
        }

        return $this->collection($this->service->list($request->query('identity_id')));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $contact = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->view($contact)) {
            return $this->forbidden();
        }

        return $this->item($contact);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->policy->create()) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, ContactRequestRules::create());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        $contact = $this->service->create($validated);

        return $this->item($contact, Response::HTTP_CREATED);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $contact = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->update($contact)) {
            return $this->forbidden();
        }

        try {
            $validated = $this->validated($request, ContactRequestRules::update());
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        }

        return $this->item($this->service->update($id, $validated));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $contact = $this->service->get($id);
        } catch (RuntimeException $exception) {
            return $this->notFound($exception->getMessage());
        }

        if (! $this->policy->delete($contact)) {
            return $this->forbidden();
        }

        $this->service->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
