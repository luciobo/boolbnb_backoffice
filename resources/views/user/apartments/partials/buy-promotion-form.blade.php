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
                <p class="mt-1 text-sm text-gray-600">
                @foreach ($promotions as $promotion)
                    <ul>
                        <li><h6>{{$promotion->type}}</h6></li>
                        <li>{{$promotion->price}}</li>
                        <li>{{$promotion->duration}}</li>
                    </ul>
                @endforeach
                </p>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>

                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')


                    <div class="input-group">

                        <input id="password" name="password" type="password" class="form-control"
                            placeholder="{{ __('Password') }}" />

                        @error('password')
                            <span class="invalid-feedback mt-2" role="alert">
                                <strong>{{ $errors->userDeletion->get('password') }}</strong>
                            </span>
                        @enderror



                        <button type="submit" class="btn btn-danger">
                            {{ __('Cancella Account') }}
                        </button>
                        <!--  -->
                    </div>
                </form>

            </div>
        </div>
    </div>

</section>