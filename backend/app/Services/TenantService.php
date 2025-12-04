<?php

namespace App\Services;

use App\Models\Tenant;
use App\Repositories\TenantRepository;
use Illuminate\Support\Str;

class TenantService
{
    public function __construct(
        private TenantRepository $tenantRepository
    ) {}

    public function create(array $data): Tenant
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->tenantRepository->create($data);
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $this->tenantRepository->update($tenant, $data);

        return $tenant->fresh();
    }

    public function delete(Tenant $tenant): bool
    {
        return $this->tenantRepository->delete($tenant);
    }

    public function findBySlug(string $slug): ?Tenant
    {
        return $this->tenantRepository->findBySlug($slug);
    }
}
