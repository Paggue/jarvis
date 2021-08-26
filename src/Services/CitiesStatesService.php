<?php


namespace Lara\Jarvis\Services;

use Lara\Jarvis\Http\Resources\DefaultCollection;
use Lara\Jarvis\Models\City;
use Lara\Jarvis\Utils\Helpers;
use Illuminate\Http\Request;
use Lara\Jarvis\Models\State;

class CitiesStatesService
{
    public function indexCities(Request $request)
    {
        $City = new City();

        $request->merge(['order' => 'name']);

        $cities = Helpers::indexQueryBuilder($request, ['state'], $City);

        $collection = DefaultCollection::class;

        return new $collection($cities);
    }

    public function indexStates(Request $request)
    {
        $State = new State();

        $request->merge(['order' => 'name']);

        $states = Helpers::indexQueryBuilder($request, [], $State);

        $collection = DefaultCollection::class;

        return new $collection($states);
    }
}
