<?php

namespace App\Http\Controllers\User;

use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use League\Flysystem\Visibility;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $apartments = Apartment::where('user_id', $user->id)->get();
        return view("user.apartments.index", compact('apartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all();
        $promotions = Promotion::all();

        return view("user.apartments.create", compact('services', 'promotions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApartmentRequest $request)
    {
        // validazione fatta da StoreApartmentRequest
        $data = $request->validated();

        //utente loggato
        $user = Auth::user();

        //immagine
        if (key_exists('img_cover', $data)) {
            $path = Storage::disk('public')->put('cover_img', $data['img_cover']);
        }

        //coordinate
        $via = urlencode($data['address']);
        $rawData = file_get_contents("https://api.tomtom.com/search/2/geocode/" . $via . ".json?storeResult=false&view=Unified&limit=1&key=sGNJHBIkBGVklWlAnKDehryPD39qsJxn");
        $rawData = json_decode($rawData);
        $lat = $rawData->results[0]->position->lat;
        $lon = $rawData->results[0]->position->lon;

        //visibilità
        if ($data['visibility']) {
            $data['visibility'] = 1;
        } else {
            $data['visibility'] = 0;
        }

        $apartment = Apartment::create([
            ...$data,
            'img_cover' => $path ?? 'cover_img/NoImageFound.jpg.png',
            'user_id' => $user->id,
            'latitude' => $lat,
            'longitude' => $lon
        ]);

        if ($request->has('services')) {
            $apartment->services()->attach($data['services']);
        }

        return redirect()->route("user.apartments.show", $apartment->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        $promotions=Promotion::all();
        // passare anche promozione attiva su quest'appartamento
        if (Auth::user()->id === $apartment->user_id) {
            return view("user.apartments.show", compact("apartment", "promotions"));
        } else {
            return view('errorPage', ['message' => 'Non sei autorizzato a vedere questo appartamento']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apartment $apartment)
    {
        if (Auth::user()->id === $apartment->user_id) {
            $services = Service::all();
            return view("user.apartments.edit", compact("apartment", 'services'));
        } else {
            return view('errorPage', ['message' => 'Non sei autorizzato a modificare questo appartamento']);
        };
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApartmentRequest $request, Apartment $apartment)
    {

        $data = $request->validated();



        if ($data['visibility'] === true) {
            $data['visibility'] = 1;
        } else {
            $data['visibility'] = 0;
        }


        $apartment->fill($data);

        if (key_exists('img_cover', $data)) {
            $path = Storage::disk('public')->put('cover_img', $data['img_cover']);
            Storage::delete($apartment->img_cover);
        }


        $apartment->img_cover = $path ?? $apartment->img_cover;


        if ($request->has('services')) {
            $apartment->services()->detach();
            $servicesToSync = [];
            foreach ($data['services'] as $singleService) {
                $servicesToSync[] = Service::findOrFail($singleService);
            }



            foreach ($servicesToSync as $sync) {
                $apartment->services()->attach($sync);
            }
        }
        if ($request->has('promotions')) {
            $apartment->promotions()->sync($data['promotions']);
        }



        $apartment->save();

        return redirect()->route("user.apartments.show", compact("apartment"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        $apartment->services()->detach();
        $apartment->promotions()->detach();
        $apartment->user()->dissociate();
        $apartment->delete();

        return redirect()->route("user.dashboard");
    }
}
