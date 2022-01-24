<?php

namespace Lara\Jarvis\Services;

use Lara\Jarvis\Http\Resources\AuditCollection;
use Lara\Jarvis\Http\Resources\DefaultCollection;
use Lara\Jarvis\Utils\Helpers;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lara\Jarvis\Validators\CommentValidator;

trait ServiceTrait
{
    protected $parentId;
    protected $modelType;

    abstract function model ();

    public function validationRules ()
    {
    }

    public function resourceCollection ()
    {
        return DefaultCollection::class;
    }

    protected function relationships ()
    {
        if (isset($this->relationships)) {
            return $this->relationships;
        }

        return [];
    }

    public function setId ($id)
    {
        $this->parentId = $id;
        return $this;
    }

    public function setModelType ($type)
    {
        $this->modelType = $type;
        return $this;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function index (Request $request)
    {
        if (method_exists($this->model(), 'getFillable'))
            if (in_array('name', $this->model()->getFillable()) && !$request->order)
                $request->merge(['order' => "name,asc"]);

        $result = Helpers::indexQueryBuilder($request, $this->relationships(), $this->model());

        $resourceCollection = $this->resourceCollection();

        return new $resourceCollection($result);
    }

    /**
     * Display a listing of the resource, including soft deleted ones.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function indexAll (Request $request)
    {
        if (method_exists($this->model(), 'getFillable'))
            if (in_array('name', $this->model()->getFillable()) && !$request->order)
                $request->merge(['order' => "name,asc"]);

        $result = Helpers::indexQueryBuilder($request, $this->relationships(), $this->model()->withTrashed());

        $resourceCollection = $this->resourceCollection();

        return new $resourceCollection($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store (Request $request)
    {
        $result = null;

        DB::transaction(function () use ($request, &$result) {

            $this->validationRules()->validate($request->all());

            $result = $this->model()->create($request->all());
        });

        return $result->load($this->relationships());
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function show (Request $request, $id = null)
    {
        return $this->model()->with($this->relationships())
            ->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update (Request $request, $id = null)
    {
        $result = null;

        DB::transaction(function () use ($request, &$result, &$id) {

            $result = $this->model()->findOrFail($id);

            $this->validationRules()->validate(array_merge(['id' => $id], $request->all()));

            $result->update($request->all());
        });

        return $result->load($this->relationships());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy (Request $request, $id = null)
    {
        $result = null;

        DB::transaction(function () use (&$request, &$result, &$id) {

            $result = $this->model()->findOrFail($id);

            $result->delete();
        });

        return $result;
    }

    /**
     * Restore the specified resource to the storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function restore (Request $request, $id = null)
    {
        $result = null;

        DB::transaction(function () use (&$request, &$result, &$id) {

            $result = $this->model()->withTrashed()->findOrFail($id);

            $result->restore();
        });

        return $result->load($this->relationships());
    }

    /**
     * Audits.
     *
     * @param Request $request
     * @param $id
     * @return AuditCollection
     */
    public function audits (Request $request, $id = null)
    {
        $data = $this->model()->withTrashed()->findOrFail($id);

        $audits = $data->audits()->with('user')->get();

        return new AuditCollection($audits);
    }

    public function createComment (Request $request, $id)
    {
        $result = null;

        DB::transaction(function () use ($request, $id, &$result) {

            $result = $this->model()->withTrashed()->findOrFail($id);

            $logged_user = $request->user();
            $request->merge(['user_id' => $logged_user->id]);

            (new CommentValidator)->validate($request->all());

            $result->comments()->create($request->all());
        });

        return $result->comments;
    }
}
