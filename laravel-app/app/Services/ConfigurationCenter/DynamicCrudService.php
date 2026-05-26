<?php

namespace App\Services\ConfigurationCenter;

use App\Models\AdminEntity;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;

class DynamicCrudService
{
    public function getList(AdminEntity $entity, array $filters = [], int $perPage = 15)
    {
        $model = $entity->getModelInstance();
        $query = $model->newQuery();

        // Apply Search
        if (!empty($filters['search'])) {
            $searchFields = $entity->fields()->where('is_searchable', true)->pluck('name');
            $query->where(function ($q) use ($searchFields, $filters) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'like', '%' . $filters['search'] . '%');
                }
            });
        }

        // Apply Filters
        $filterableFields = $entity->fields()->where('is_filterable', true)->get();
        foreach ($filterableFields as $field) {
            if (isset($filters[$field->name]) && $filters[$field->name] !== '') {
                $query->where($field->name, $filters[$field->name]);
            }
        }

        // Apply Sorting
        $sortField = $filters['sort_by'] ?? 'id';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortField, $sortDir);

        // Soft Deletes
        if (isset($filters['trashed']) && $filters['trashed'] === 'only') {
            $query->onlyTrashed();
        } elseif (isset($filters['trashed']) && $filters['trashed'] === 'with') {
            $query->withTrashed();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function store(AdminEntity $entity, array $data)
    {
        $modelClass = $entity->model_class;
        return $modelClass::create($data);
    }

    public function update(AdminEntity $entity, $id, array $data)
    {
        $modelClass = $entity->model_class;
        $record = $modelClass::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(AdminEntity $entity, $id)
    {
        $modelClass = $entity->model_class;
        $record = $modelClass::findOrFail($id);
        return $record->delete();
    }

    public function restore(AdminEntity $entity, $id)
    {
        $modelClass = $entity->model_class;
        $record = $modelClass::withTrashed()->findOrFail($id);
        return $record->restore();
    }
}
