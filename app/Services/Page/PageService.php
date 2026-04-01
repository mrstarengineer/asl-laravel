<?php


namespace App\Services\Page;


use App\Models\Page;
use App\Services\BaseService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService extends BaseService
{
    public function all(array $filters = [])
    {
        $query = Page::query();

        if (!empty($filters['title'])) {
            $query->where('title', $filters['title']);
        }

        if (!empty($filters['q'])) {
            $query->where(DB::raw('LOWER(title)'), 'LIKE', '%'. strtolower($filters['q']) .'%');
        }

        $limit = Arr::get($filters, 'limit', 20);

        return $limit != '-1' ? $query->paginate($limit) : $query->get();
    }

    public function getById($id)
    {
        return Page::find($id);
    }

    public function store(array $data)
    {
        return $this->savePage($data);
    }

    public function update($id, array $data)
    {
        return $this->savePage($data, $id);
    }

    public function destroy($id)
    {
        return Page::find($id)->delete();
    }

    private function savePage($data, $id = null)
    {
        $page = Page::findOrNew($id);
        $page->fill($data);
        $page->slug = Str::slug( $page->title );
        $page->save();

        return $page;
    }
}
