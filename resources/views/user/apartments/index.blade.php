@extends('layouts.app')

@section('content')
    @php
        $now = date('Y/m/d h:i:s');
    @endphp
    <div class="bg-imgg-form">
        <div class="container">
            @if (count($apartments))
                <h1 class="py-3 text-center text-white">I Tuoi Appartamenti</h1>
                <div class="apartments_container">
                    <div class="row w-100 mb-3">
                        <div class="col-md-8 m-auto">
                            @foreach ($apartments as $index => $item)
                                <div class="card mw overflow-hidden mb-3" id="card{{ $item->id }}">
                                    <div
                                        class="row g-0 mw overflow-hidden {{ $loop->index % 2 === 1 ? 'flex-row-reverse' : '' }}">
                                        <div class=" mw col-md-4 overflow-hidden">
                                            @if (!str_contains($item->img_cover, 'cover_img/'))
                                                <img class="card-img-top card_img_horizontal" src=" {{ $item->img_cover }}"
                                                    alt="Card image cap">
                                            @else
                                                <img class="card-img-top card_img_horizontal"
                                                    src="{{ asset('storage/' . $item->img_cover) }}" alt="Card image cap">
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-header">
                                                <ul class="nav nav-tabs card-header-tabs" id="myTab{{ $item->id }}"
                                                    role="tablist">
                                                    <!-- info -->
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="info-tab{{ $item->id }}"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#info-tab-pane{{ $item->id }}"
                                                            type="button" role="tab"
                                                            aria-controls="info-tab-pane{{ $item->id }}"
                                                            aria-selected="true"><i
                                                                class="fa-solid fa-circle-info"></i></button>
                                                    </li>
                                                    <!-- description -->
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="description-tab{{ $item->id }}"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#description-tab-pane{{ $item->id }}"
                                                            type="button" role="tab"
                                                            aria-controls="description-tab-pane{{ $item->id }}"
                                                            aria-selected="false"><i
                                                                class="fa-solid fa-quote-left"></i></button>
                                                    </li>
                                                    <!-- promotion -->
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="promotion-tab{{ $item->id }}"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#promotion-tab-pane{{ $item->id }}"
                                                            type="button" role="tab"
                                                            aria-controls="promotion-tab-pane{{ $item->id }}"
                                                            aria-selected="false"><i
                                                                class="fa-solid fa-bullhorn"></i></button>
                                                    </li>
                                                    <!-- show -->
                                                    <li class="nav-item" role="presentation">
                                                        <a href="{{ route('user.apartments.show', $item->id) }}"
                                                            class="btn btn-light"><i class="fa-solid fa-eye"></i></a>
                                                    </li>
                                                    <!-- edit -->
                                                    <li class="nav-item" role="presentation">
                                                        <a href="{{ route('user.apartments.edit', $item->id) }}"
                                                            class="btn btn-light"><i class="fa-solid fa-pencil"></i></a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-body">
                                                <div class="tab-content" id="myTabContent{{ $item->id }}">
                                                    <!-- info -->
                                                    <div class="tab-pane fade show active"
                                                        id="info-tab-pane{{ $item->id }}" role="tabpanel"
                                                        aria-labelledby="info-tab{{ $item->id }}" tabindex="0">
                                                        <h5 class="card-title">#{{ $index + 1 }} ~ {{ $item->title }}
                                                        </h5>
                                                        <h6>
                                                            <i class="fa-solid fa-map-pin"></i> {{ $item->address }}
                                                        </h6>
                                                        <div class="card-text d-flex gap-2 overflow-y-hidden overflow-auto">
                                                            Servizi:
                                                            @foreach ($item->services as $service)
                                                                <h6><span
                                                                        class="badge my-card text-dark">{{ $service->name }}</span>
                                                                </h6>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <!-- description -->
                                                    <div class="tab-pane fade" id="description-tab-pane{{ $item->id }}"
                                                        role="tabpanel"
                                                        aria-labelledby="description-tab{{ $item->id }}"
                                                        tabindex="0">
                                                        @if ($item->description === null)
                                                            <i>non hai ancora inserito una descrizione per questo
                                                                appartamento</i>
                                                        @else
                                                            {{ Str::limit($item->description, 100) }}
                                                        @endif
                                                    </div>
                                                    {{-- promotion --}}
                                                    <div class="tab-pane fade" id="promotion-tab-pane{{ $item->id }}"
                                                        role="tabpanel" aria-labelledby="promotion-tab{{ $item->id }}"
                                                        tabindex="0">
                                                        @foreach ($item->promotions as $promotion)
                                                            @if (strtotime($promotion->pivot->expired_at) > strtotime($now))
                                                                <div>Hai una promozione attiva!</div>
                                                                <div><strong>Tipo:</strong> {{ $promotion->type }}</div>
                                                                <div><strong>Scade il:</strong>
                                                                    {{ $promotion->pivot->expired_at }}</div>
                                                            @endif
                                                        @endforeach
                                                        @if (is_null($item->promotions->first()))
                                                            <i>non hai promozioni attive su questo appartamento</i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </col-md-8>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center">

                    <h1 class="py-3 text-center text-white">Non hai inserito nessun appartamento</h1>
                    <a class="btn btn-warning" href="{{ route('user.apartments.create') }}">Creane uno !</a>
                </div>
            @endif
        </div>
    @endsection
