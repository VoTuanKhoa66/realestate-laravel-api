<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    protected array $queryset_map = [];
    protected array $search_map = [];
    protected ?string $modelClass = null;
    protected array $serializer_map = [];
    protected array $rules = [];
    protected array $filterable_fields = [];


    protected function getQuery(string $action)
    {
        if (isset($this->queryset_map[$action])) {
            return ($this->queryset_map[$action])();
        }

        if ($this->modelClass) {
            return app($this->modelClass)::query();
        }

        abort(500, "Model class not defined in BaseController.");
    }

    protected function clearCache()
    {
        Cache::flush();
    }

    //Validate request
    protected function validateRequest(Request $request, array $rules = [], array $messages = [], array $attributes = [])
    {

        $rules = $rules ?: $this->rules;
        $messages = $messages ?: ($this->messages ?? []);
        $attributes = $attributes ?: ($this->attributes ?? []);

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            abort(response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422));
        }

        return $validator->validated();
    }

    //search_map
    protected function applySearch(Builder $query, Request $request)
    {
        $keyword = $request->query('textSearch');
        if ($keyword && count($this->search_map) > 0) {
            $query->where(function ($q) use ($keyword) {
                foreach ($this->search_map as $field => $operator) {
                    if ($operator === 'icontains') {
                        $q->orWhere($field, 'LIKE', "%$keyword%");
                    } else {
                        $q->orWhere($field, $operator, $keyword);
                    }
                }
            });
        }
        return $query;
    }


    protected function applyFilters(Builder $query, Request $request)
    {
        foreach ($this->filterable_fields as $field) {
            if ($request->has($field)) {
                $query->where($field, $request->get($field));
            }
        }
        return $query;
    }


    //Get all
    public function index(Request $request)
    {
        $query = $this->getQuery('list');
        $query = $this->applySearch($query, $request);
        $query = $this->applyFilters($query, $request);

        if ($limit = $request->query('limitnumber')) {
            $data = $query->paginate($limit);
        } else {
            $data = $query->get();
        }

        return $this->response($data);
    }

    public function show($id)
    {
        $data = app($this->modelClass)::find($id);

        if (!$data) {
            return response()->json([
                'message' => "Can not find!",
            ], 404);
        }
        return $this->response($data);
    }

    //Create
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $instance = app($this->modelClass)::create($data);

        $this->clearCache();
        return $this->response($instance, code: 201);
    }

    //update
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validateRequest($request, $this->rules);
            $model = app($this->modelClass)::findOrFail($id);
            $model->update($data);

            $this->clearCache();
            return $this->response($model);
        } catch (ModelNotFoundException $e) {
            return $this->error(message: $e->getMessage());
        }
    }

    //Delete
    public function destroy($id)
    {
        try {
            $model = app($this->modelClass)::findOrFail($id);
            $model->delete();

            $this->clearCache();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return $this->error(message: $e->getMessage());
        }
    }

    //Mutiple Delete
    public function multipleDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $model = app($this->modelClass);

        DB::transaction(function () use ($model, $ids) {
            $model->whereIn('id', $ids)->delete();
        });

        $this->clearCache();
        return response()->json(null, 204);
    }

    function response($data = null, $message = 'Successful.', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'status_code' => $code
        ], $code);
    }

    function error($error = null, $message = 'Error.', $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $error,
            'status_code' => $code
        ], $code);
    }
}
