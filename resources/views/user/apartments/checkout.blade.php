@extends ('layouts.app')

@section('content')
<div class="container mt-5">
    <h5>Stai acquistando la promozione {{$promotion->type}}</h5>
    <button  v-if="disableButton" disabled class="btn btn-primary">Procedi con l'acquisto</button>
    <button  v-if="!disableButton" class="btn btn-primary">Procedi con l'acquisto</button>
</div>
@endsection

<script type="module">
        const {createApp} = Vue;
    createApp({

        data(){
            return{
                disableButton:true
            }
        }

    }).mount('#app')
</script>