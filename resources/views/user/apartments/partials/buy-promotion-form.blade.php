<section>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pay-promotion">Promuovi l'appartamento</button>

<div class="modal fade" id="pay-promotion" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                                role="dialog" aria-labelledby="delete-account" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-account">Promuovi l'appartamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Con quale offerta vuoi promuovere il tuo appartamento?') }}
                </h2>
                <div class="row">
                @foreach ($promotions as $promotion)
                <div class="col col-sm-6 col-md-4">
                    <div class="col-content">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $promotion->type }}</h5>
                                <p class="card-text">
                                    {{ $promotion->price }}$
                                    <br>
                                    {{ $promotion->duration}} h
                                </p>
                                <a class="btn btn-primary">Acquista</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                </div>

                
            </div>
            <div class="modal-footer">
           
            </div>
        </div>
    </div>

</section>