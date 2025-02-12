<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Component\CreateComponent;
use Cachet\Actions\Component\DeleteComponent;
use Cachet\Actions\Component\UpdateComponent;
use Cachet\Data\Component\CreateComponentData;
use Cachet\Data\Component\UpdateComponentData;
use Cachet\Http\Resources\Component as ComponentResource;
use Cachet\Models\Component;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Components
 */
class ComponentController extends Controller
{
    /**
     * List Components
     *
     * @apiResourceCollection \Cachet\Http\Resources\Component
     *
     * @apiResourceModel \Cachet\Models\Component
     *
     * @queryParam per_page int How many items to show per page. Example: 20
     * @queryParam page int Which page to show. Example: 2
     * @queryParam sort string Field to sort by. Enum: name, status, enabled Example: name
     * @queryParam include string Include related resources. Enum: group, incidents Example: group,incidents
     * @queryParam filters string[] Filter the resources. Example: name=api
     */
    public function index()
    {
        $components = QueryBuilder::for(Component::class)
            ->allowedIncludes(['group', 'incidents'])
            ->allowedFilters(['name', 'status', 'enabled'])
            ->allowedSorts(['name', 'order', 'id'])
            ->simplePaginate(request('per_page', 15));

        return ComponentResource::collection($components);
    }

    /**
     * Create Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     *
     * @apiResourceModel \Cachet\Models\Component
     *
     * @authenticated
     */
    public function store(CreateComponentData $data, CreateComponent $createComponentAction)
    {
        $component = $createComponentAction->handle(
            $data,
        );

        return ComponentResource::make($component);
    }

    /**
     * Get Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     *
     * @apiResourceModel \Cachet\Models\Component
     */
    public function show(Component $component)
    {
        return ComponentResource::make($component)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Component
     *
     * @apiResource \Cachet\Http\Resources\Component
     *
     * @apiResourceModel \Cachet\Models\Component
     *
     * @authenticated
     */
    public function update(UpdateComponentData $data, Component $component, UpdateComponent $updateComponentAction)
    {
        $updateComponentAction->handle($component, $data);

        return ComponentResource::make($component->fresh());
    }

    /**
     * Delete Component
     *
     * @response 204
     *
     * @authenticated
     */
    public function destroy(Component $component, DeleteComponent $deleteComponentAction)
    {
        // @todo what happens to incidents linked to this component?
        // @todo re-calculate existing component orders?

        $deleteComponentAction->handle($component);

        return response()->noContent();
    }
}
