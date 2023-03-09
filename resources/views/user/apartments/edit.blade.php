@extends('layouts.app')

@section('content')
    @php
        $apartmentServices = $apartment
            ->services()
            ->get()
            ->toArray();
        
    @endphp
    @if ($errors->any())
        <div class="alert alert-danger">
            I dati inseriti non sono validi:

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <h1>Sezione edit</h1>
        <div class="text-center bg-white rounded-3 py-5">
            <form action="{{ route('user.apartments.update', $apartment->id) }}"
                class="form-group w-75 d-inline-block shadow rounded-3 p-3 py-5" method="POST" enctype="multipart/form-data">
                @csrf()
                @method('PUT')
                <div class="mb-3 apartmentName">

                    <label class="form-label">Titolo appartemento</label>
                    <input @input='checkData(apartmentName,"apartmentName")' v-model='apartmentName' type="text"
                        class="form-control text-center w-75 mx-auto" name="title" minlength="1" required
                        @error('title') is-invalid @elseif(old('title')) is-valid @enderror>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="error d-none text-danger">
                        C'è qualche problema con il tuo nome. Ti consigliamo di controllare che non ci siano caratteri
                        speciali.
                    </div>

                </div>
                <div class="rooms mb-3">
                    <label class="form-label">Numero di stanze</label>
                    <input @input='checkData(rooms,"rooms")' v-model='rooms' type="number" step="1" min="0"
                        class="form-control text-center w-75 mx-auto" name="n_rooms">
                    <div class="error d-none text-danger">
                        C'è qualche problema con il numero delle stanze. Controlla che il numero sia compreso tra 1 e 255.
                    </div>

                </div>
                <div class="bath mb-3">
                    <label class="form-label">Numero di bagni</label>
                    <input @input='checkData(bath,"bath")' v-model='bath' type="number" step="1" min="0"
                        class="form-control text-center w-75 mx-auto" name="n_bathrooms">
                    <div class="error d-none text-danger">
                        C'è qualche problema con il numero dei bagni. Controlla che il numero sia compreso tra 1 e 255.
                    </div>

                </div>
                <div class="beds mb-3">
                    <label class="form-label">Numero di letti</label>
                    <input @input='checkData(beds,"beds")' v-model='beds' type="number" step="1" min="0"
                        class="form-control text-center w-75 mx-auto" name="n_beds">
                    <div class="error d-none text-danger">
                        C'è qualche problema con il numero dei letti. Controlla che il numero sia compreso tra 1 e 255.
                    </div>

                </div>
                <div class="sm mb-3">
                    <label class="form-label">Metri quadrati</label>
                    <input @input='checkData(sm,"sm")' v-model='sm' type="number" step="0.5" min="30"
                        class="form-control text-center w-75 mx-auto" name="square_meters">
                    <div class="error d-none text-danger">
                        C'è qualche problema con il numero dei metri quadrati. Controlla che il numero sia compreso tra 30 e
                        2.000.000.
                    </div>

                </div>
                <div class="apartmentDescription mb-3">
                    <label class="form-label">Descrizione</label>
                    <textarea v-model='apartmentDescription' name="description" cols="30" rows="5"
                        class="form-control w-75 mx-auto">{{ $apartment->description }}</textarea>
                </div>
                <div class="address mb-3">
                    <label class="form-label">Indirizzo</label>
                    <input @input='checkData(searchField,"address")' type="text" step="0.5" autocomplete="off"
                        class="form-control text-center w-75 mx-auto" name="address" v-model="searchField"
                        @keyup="refreshSearch">
                    <div class="error d-none text-danger">
                        C'è qualche problema con il tuo indirizzo, assicurati che non abbia caratteri speciali e che tu
                        abbia selezionato l'indirizzo cliccandolo dal meno a tendina.
                    </div>
                    <div class="list-group addressList">
                        <a :value='i' v-for='(item,i) in searchData'
                            class="list-group-item list-group-item-action" @click='choosenAddress(i)'>
                            @{{ item.address.freeformAddress }}
                        </a>

                    </div>


                </div>

                <div class="visibility mb-3">
                    <label class="form-label">Visibilità</label>
                    <label for="">No</label>
                    <input type="radio" step="0.5" name="visibility" value="false">
                    <label for="">Yes</label>
                    <input type="radio" step="0.5" name="visibility" value="true" checked>
                </div>

                <div class="services mb-3">
                    <div class="rules"><span class="text-info fw-bold">N.B: </span>Devi selezionare almeno un servizio
                    </div>
                    @foreach ($services as $key => $service)
                        <div class="form-check form-check-inline @error('services') is-invalid @enderror">
                            <input v-model='services{{ $loop->index }}'
                                class="form-check-input @error('services') is-invalid @enderror" type="checkbox"
                                name="services[]" value="{{ $service->id }}"
                                @foreach ($apartmentServices as $item)
                                    @if ($item['name'] === $service->name)
                                       checked='true'
                                    @endif @endforeach>
                            <label class="form-check-label">{{ $service->name }}</label>
                        </div>
                    @endforeach
                </div>

                <div class="img_cover mb-3">
                    <label class="form-label">Carica l'immagine del progetto</label>
                    <input @change='imgCoverChange' type="file"
                        class="form-control text-center w-75 mx-auto
                        @error('img_cover') is-invalid @elseif(old('img_cover')) is-valid @enderror"
                        name="img_cover">
                    @error('img_cover')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button :disabled='errorDigit' class="btn btn-lg btn-outline-dark mt-4" type="submit">Salva
                    Progetto</button>
            </form>
        </div>
    </div>
@endsection
<script type="module">
    const {createApp} = Vue;
    createApp({
        data() {
            return {
                searchField: <?php echo json_encode($apartment->address, JSON_HEX_TAG); ?>,
                searchData: [],
                apartmentName: <?php echo json_encode($apartment->title, JSON_HEX_TAG); ?>,
                rooms: <?php echo json_encode($apartment->n_rooms, JSON_HEX_TAG); ?>,
                bath: <?php echo json_encode($apartment->n_bathrooms, JSON_HEX_TAG); ?>,
                beds: <?php echo json_encode($apartment->n_beds, JSON_HEX_TAG); ?>,
                sm: <?php echo json_encode($apartment->square_meters, JSON_HEX_TAG); ?>,
                apartmentDescription: <?php echo json_encode($apartment->description, JSON_HEX_TAG); ?>,
                services:[<?php echo json_encode($apartment->services, JSON_HEX_TAG); ?>],
                img_cover:<?php echo json_encode($apartment->img_cover, JSON_HEX_TAG); ?>,
                error:false
            }},
            computed:{
                errorDigit: function (){
                    if(this.apartmentName.length <=0 || this.apartmentName.length>255){
                     
                        return true
                        }else {
                            if(this.rooms <0 || this.rooms>255){
                               
                                return true
                            }else {
                                if(this.bath <=0 || this.bath>255){
                              
                                    return true
                            }else {
                                if(this.beds <=0 || this.beds>255){
                                  
                                    return true
                            }else {
                                if(this.sm <=30 || this.sm>2000000){
                                    return true
                            }else {
                                if(this.searchField <=0 || this.searchField>255){
                                    return true
                            }else {
                                if(!this.services.length){
                                    return true
                            }else {
                                if(!this.img_cover){
                                    return true
                            }else{
                                    return false
                                    }
                                }
                            }
                            }}}}};

                    }

            
                    
                
            },
            mounted() {
                console.log(this.services);
            },
            methods: {
                imgCoverChange(event){
                    const chosenFiles = event.target.files
                    this.img_cover = chosenFiles[0];

                },
                choosenAddress(i){
                    const rawDiv = document.querySelector('.addressList')
                    const tagA = document.querySelectorAll('.addressList > a');
                    this.searchField = tagA[i].textContent;
                    rawDiv.classList.add('d-none')

                    
                },
                

        

        async refreshSearch() {
            if (this.searchField) {
                encodeURIComponent(this.searchField);


                await axios.get(`https://api.tomtom.com/search/2/search/${this.searchField}.json?lat=41.9028&lon=12.4964&language=it-IT&minFuzzyLevel=1&maxFuzzyLevel=2&view=Unified&relatedPois=all`,{
                    params:{
                        "key": 'C1SeMZqi2HmD2jfTGWrbkAAknINrhUJ3'
                    },
                    
                    
                })
                    .then((resp) => {
                        this.searchData = resp.data.results;
                        this.error=false
                    })
                    .catch(()=>{
                        this.error = true
                    })
            };
        },
        checkData(properties,cName){
            const specialCharacters = [
                '+',
                 '-',
                 '@',
                 '#',
                 '$',
                 '&&',
                '|',
                '=',
                 '!',
                 '%',
                 '<',
                 '>',
                 '(',
                 '`',
                 ')',
                 '{',
                 '}',
                 '[',
                 '[]',
                 ']',
                 '^',
                 '"',
                 ';',
                 '~',
                 '*',
                 '?',
                 ':'
            ]

            let className = `.${cName}>*`;

            const rawDiv = document.querySelectorAll(className)
            const input = rawDiv[1];
            const errorDiv = rawDiv[2];
            const addressList = document.querySelector('.addressList')
            if(typeof(properties) === 'number' && input.getAttribute('name') != 'square_meters'){
                if( properties <= 0 || properties >255){
                input.classList.add('is-invalid')
                errorDiv.classList.replace('d-none','d-block')
                }
                else{
                    input.classList.remove('is-invalid')
                    errorDiv.classList.replace('d-block','d-none')
                }
            }
            else if(typeof(properties) === 'number'){
                if( properties < 30 || properties >2000000){
                input.classList.add('is-invalid')
                errorDiv.classList.replace('d-none','d-block')
                }else{
                    input.classList.remove('is-invalid')
                    errorDiv.classList.replace('d-block','d-none')
                }
            }


            if(this.error && typeof(properties) === 'string'){
                input.classList.add('is-invalid')
                errorDiv.classList.replace('d-none','d-block')
                addressList.classList.add('d-none')

            }else if(typeof(properties) === 'string'){
                for(let i= 0;i<specialCharacters.length;i++){
                    if(properties.includes(specialCharacters[i])){
                        input.classList.add('is-invalid')
                        errorDiv.classList.replace('d-none','d-block')
                        addressList?.classList.add('d-none')
                        break;
                    }else{
                        input.classList.remove('is-invalid')
                        errorDiv.classList.replace('d-block','d-none')
                        addressList?.classList.replace('d-none','d-block')
                        
                    }
                }
                
            }
            }
           
            
            
}
        }).mount("#app");
</script>
