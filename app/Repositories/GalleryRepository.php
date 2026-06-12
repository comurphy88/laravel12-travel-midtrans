<?php

namespace App\Repositories;

use App\Models\Gallery;
use Illuminate\Pagination\LengthAwarePaginator;

class GalleryRepository
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Gallery::orderByDesc('created_at')->paginate($perPage);
    }

    public function create(array $data): Gallery
    {
        return Gallery::create($data);
    }

    public function update(Gallery $gallery, array $data): bool
    {
        return $gallery->update($data);
    }

    public function delete(Gallery $gallery): bool
    {
        return $gallery->delete();
    }

    public function findById(int $id): ?Gallery
    {
        return Gallery::find($id);
    }
}
