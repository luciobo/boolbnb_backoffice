<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $allApartments = Apartment::all();

        $sponsored = $request->input('sponsored');
        $simpleSearch = $request->input('city');
        $advancedSearch = $request->all('place', 'radius', 'rooms', 'beds', 'sqrMeters', 'services');
        // dd(json_decode($advancedSearch['services']));

        if ($sponsored) {
            foreach ($allApartments as $item) {
                $apartments = $item
                    ->whereHas('promotions', function ($q) {
                        $q->where('expired_at', '>', Carbon::now());
                    })
                    ->get()
                    ->sortByDesc('promotions.id');
            };
        } else if ($simpleSearch) {
            //trasforma input in coordinate
            $city = urlencode($simpleSearch);
            $rawData = file_get_contents("https://api.tomtom.com/search/2/geocode/" . $city . ".json?storeResult=false&countrySet=IT&view=Unified&limit=1&key=sGNJHBIkBGVklWlAnKDehryPD39qsJxn");
            $rawData = json_decode($rawData);
            $lat = $rawData->results[0]->position->lat;
            $lon = $rawData->results[0]->position->lon;
            //ordina tutte le coordinate a db a distanza crescente da input utente
            $allCoordinates = Apartment::select('latitude', 'longitude')
                ->selectRaw('(6371 * acos (
                    cos ( radians(?) )
                    * cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(?) )
                    + sin ( radians(?) )
                    * sin( radians( latitude )))) AS distance', [
                    $lat,
                    $lon,
                    $lat
                ])
                ->orderBy('distance', 'ASC')
                ->get()
                ->toArray();

            $nearestApartments = [];
            //ciclo su tutte le coordinate a db
            foreach ($allCoordinates as $coordinates) {
                $radiusSearch =  file_get_contents("https://api.tomtom.com/search/2/geometryFilter.json?geometryList=%5B%7B%22type%22%3A%22CIRCLE%22%2C%20%22position%22%3A%22" . $lat . "%2C%20" . $lon . "%22%2C%20%22radius%22%3A20000%7D%5D&poiList=%5B%7B%22position%22%3A%7B%22lat%22%3A" . $coordinates['latitude'] . "%2C%22lon%22%3A" . $coordinates['longitude'] . "%7D%7D%5D&key=1p9OyCRm8S7icw73fBmkTYDlXYJGPO9O");
                $radiusSearch = json_decode($radiusSearch);

                //se c'?? un match pusho coordinate e relativo appart. in array
                if (!empty($radiusSearch->results)) {
                    array_push($nearestApartments, Apartment::where([
                        ['latitude', $radiusSearch->results[0]->position->lat],
                        ['longitude', $radiusSearch->results[0]->position->lon]
                    ])
                        ->with('services')
                        ->get());
                }
            }
            $apartments = $nearestApartments;
        } else if ($advancedSearch) {
            $city = urlencode($advancedSearch['place']);
            $rawData = file_get_contents("https://api.tomtom.com/search/2/geocode/" . $city . ".json?storeResult=false&countrySet=IT&view=Unified&limit=1&key=sGNJHBIkBGVklWlAnKDehryPD39qsJxn");
            $rawData = json_decode($rawData);
            $lat = $rawData->results[0]->position->lat;
            $lon = $rawData->results[0]->position->lon;

            $allCoordinates = Apartment::select('latitude', 'longitude')
                ->selectRaw('(6371 * acos (
                    cos ( radians(?) )
                    * cos( radians( latitude ) )
                    * cos( radians( longitude ) - radians(?) )
                    + sin ( radians(?) )
                    * sin( radians( latitude )))) AS distance', [
                    $lat,
                    $lon,
                    $lat
                ])
                ->where([
                    ['n_rooms', '>=', $advancedSearch['rooms']],
                    ['n_beds', '>=', $advancedSearch['beds']],
                    ['square_meters', '>=', $advancedSearch['sqrMeters']]
                ])
                ->orderBy('distance', 'ASC')
                ->get()
                ->toArray();

            $advancedApartments = [];
            foreach ($allCoordinates as $coordinates) {
                $radiusSearch =  file_get_contents("https://api.tomtom.com/search/2/geometryFilter.json?geometryList=%5B%7B%22type%22%3A%22CIRCLE%22%2C%20%22position%22%3A%22" . $lat . "%2C%20" . $lon . "%22%2C%20%22radius%22%3A" . $advancedSearch['radius'] . "%7D%5D&poiList=%5B%7B%22position%22%3A%7B%22lat%22%3A" . $coordinates['latitude'] . "%2C%22lon%22%3A" . $coordinates['longitude'] . "%7D%7D%5D&key=1p9OyCRm8S7icw73fBmkTYDlXYJGPO9O");
                $radiusSearch = json_decode($radiusSearch);

                //se c'?? un match pusho coordinate e relativo appart. in array
                if (!empty($radiusSearch->results)) {
                    array_push($advancedApartments, Apartment::where([
                        ['latitude', $radiusSearch->results[0]->position->lat],
                        ['longitude', $radiusSearch->results[0]->position->lon],
                    ])
                        ->with('services')
                        ->get());
                }
            }
            $apartments = $advancedApartments;
        } else {
            $apartments = Apartment::with('services')->paginate(5);
        }
        return response()->json($apartments);
    }

    public function show(Apartment $apartment)
    {
        $apartment->load('services')->get();
        return response()->json($apartment);
    }
}
