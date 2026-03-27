<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\LocationResource;
use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Illuminate\Http\Request;

class LocationController extends BaseApiController
{
    /**
     * List locations
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = City::query()
            ->select(['id', 'name', 'state_id', 'latitude', 'longitude'])
            ->with(['state.country'])
            ->wherePublished()
            ->orderBy('name');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $perPage = min($request->integer('per_page', 20), 100);
        $locations = $query->paginate($perPage);

        // Transform to location format
        $locations->getCollection()->transform(function ($city) {
            $fullName = $city->name;
            if ($city->state) {
                $fullName .= ', ' . $city->state->name;
                if ($city->state->country) {
                    $fullName .= ', ' . $city->state->country->name;
                }
            }

            return [
                'id' => $city->id,
                'full_address' => $fullName,
                'latitude' => $city->latitude ?? null,
                'longitude' => $city->longitude ?? null,
            ];
        });

        return $this
            ->httpResponse()
            ->setData(LocationResource::collection($locations))
            ->toApiResponse();
    }

    /**
     * Search locations
     *
     * @group Car Rentals
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
        ]);

        $search = $request->input('q');
        $results = [];

        // Search in cities if location plugin is available
        if (class_exists(City::class)) {
            $cities = City::query()
                ->select(['id', 'name', 'state_id', 'latitude', 'longitude'])
                ->where('name', 'LIKE', "%{$search}%")
                ->wherePublished()
                ->with(['state.country'])
                ->limit(10)
                ->get();

            foreach ($cities as $city) {
                $fullName = $city->name;
                if ($city->state) {
                    $fullName .= ', ' . $city->state->name;
                    if ($city->state->country) {
                        $fullName .= ', ' . $city->state->country->name;
                    }
                }

                $results[] = [
                    'type' => 'city',
                    'id' => $city->id,
                    'name' => $fullName,
                    'city' => $city->name,
                    'state' => $city->state->name ?? null,
                    'country' => $city->state->country->name ?? null,
                    'latitude' => $city->latitude ?? null,
                    'longitude' => $city->longitude ?? null,
                ];
            }
        }

        // Search in states if location plugin is available
        if (class_exists(State::class)) {
            $states = State::query()
                ->select(['id', 'name', 'country_id'])
                ->where('name', 'LIKE', "%{$search}%")
                ->with(['country'])
                ->limit(5)
                ->get();

            foreach ($states as $state) {
                $fullName = $state->name;
                if ($state->country) {
                    $fullName .= ', ' . $state->country->name;
                }

                $results[] = [
                    'type' => 'state',
                    'id' => $state->id,
                    'name' => $fullName,
                    'state' => $state->name,
                    'country' => $state->country->name ?? null,
                ];
            }
        }

        // Search in countries if location plugin is available
        if (class_exists(Country::class)) {
            $countries = Country::query()
                ->select(['id', 'name'])
                ->where('name', 'LIKE', "%{$search}%")
                ->limit(5)
                ->get();

            foreach ($countries as $country) {
                $results[] = [
                    'type' => 'country',
                    'id' => $country->id,
                    'name' => $country->name,
                    'country' => $country->name,
                ];
            }
        }

        return $this
            ->httpResponse()
            ->setData($results)
            ->toApiResponse();
    }
}
