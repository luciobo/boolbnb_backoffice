@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="container py-5">

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="id">{{ $apartment->id }}</div>
                    <h2 class="mb-3">{{ $apartment->title }}</h2>
                    <h3><span class="badge text-bg-success">{{ $apartment->address }}</span></h3>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-info" href="{{ route('user.apartments.edit', $apartment) }}">Edit</a>
                    <form action="{{ route('user.apartments.destroy', $apartment->id) }}" method="POST"
                        class="delete_apartment">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-8">
                    <p class="lead">{{ $apartment->description }}</p>
                </div>
                <div class="col-md-4">
                    <img src="{{ asset('storage/' . $apartment['img_cover']) }}" alt="{{ $apartment->title }}"
                        class="img-fluid">
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <div>
                    <h5>Promozioni attive</h5>
                    <p></p>
                </div>
                {{-- link che dovrebbe rimandare a vista front-end --}}
                <a href="#" class="btn btn-dark align-self-baseline">Vedi da ospite</a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>N. stanze</th>
                        <th>N. bagni</th>
                        <th>N. letti</th>
                        <th>Metri quadri</th>
                        <th>Servizi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $apartment->n_rooms }}</td>
                        <td>{{ $apartment->n_bathrooms }}</td>
                        <td>{{ $apartment->n_beds }}</td>
                        <td>{{ $apartment->square_meters }}</td>
                        <td>
                            <ul>
                                @foreach ($apartment->services as $service)
                                    <li>{{ $service->name }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>


            <div id="map" ref="mapRef">
                <div id="italy"></div>
            </div>


        </div>
    </div>

    <script>
        const form = document.querySelector('.delete_apartment')
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const confirm_del = confirm('Sicuro di voler eliminare questo appartamento?');
            if (confirm_del) {
                form.submit();
            }
        })
    </script>
    <script type="module">
        const {createApp,onMounted,ref} = Vue
        createApp({
            data() {
                return {
                    'apartmentList': []
                }
            },
            name: 'Map',
            mounted() {
                const sizeMapModify = document.querySelector('#italy');
                sizeMapModify.style= 'height:400px'
            },

            setup() {
                const mapRef = ref('italy');
                const id = <?php echo json_encode($apartment->id, JSON_HEX_TAG); ?>;
                

                onMounted(async () => {
                    let apartment;
                    await axios.get(`${window.location.origin}/api/apartments/${id}`)
                        .then(resp => {
                            apartment = resp.data;
                        });
                        const centerLat=apartment.latitude - 0.001
                        const centerLon=apartment.longitude - 0.001
                    const tt = window.tt;
                    var map = tt.map({
                        key: 'C1SeMZqi2HmD2jfTGWrbkAAknINrhUJ3',
                        container: mapRef.value,
                        style: 'tomtom://vector/1/basic-main/',
                        zoom: 13,
                        center: [centerLon, centerLat],
                    });
                    map.addControl(new tt.FullscreenControl());
                    map.addControl(new tt.NavigationControl());

                    addMarker(map, apartment.longitude, apartment.latitude, apartment.address);


                })

                function addMarker(map, longitude, latitude, address) {

                    const tt = window.tt;
                    var location = [longitude, latitude];
                    var popupOffset = 25;

                    var marker = new tt.Marker().setLngLat(location).addTo(map);
                    var popup = new tt.Popup({
                        offset: popupOffset
                    }).setHTML(address);
                    marker.setPopup(popup).togglePopup();

                    const mapboxglPopupContent = document.querySelector('.mapboxgl-popup-content');
                    mapboxglPopupContent.classList.add('text-black');
                }

                return {
                    mapRef,
                };
            },
        }).mount('#map')
    </script>
@endsection
