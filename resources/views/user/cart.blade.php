@extends('layout.app')

@section('title')
    Корзина
@endsection

@section('main')

    <style>
        /* tbody>tr>td{
            border-bottom-width: 0 !important;
        }
        tbody>tr{
            border-bottom: 1px #dee2e6 solid;
        } */
        .tabl>div{
            padding: 0.5rem
        }
        .tbody{
            border-bottom: 1px #dee2e6 solid;
        }
        /* .pmform{
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
        } */
        .pmbut{
            background-color: #ffc107;
            border-radius: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            width: 30px;
            height: 30px;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0 0 0.2rem 0;
            transition: 0.3s;
        }
        .pmbut:hover{
            background-color: #e4ac06;
            transition: 0.3s;
        }
    </style>

    <div class="container" id='cartPage'>

        <div class="d-flex justify-content-center flex-column align-items-center col-12">
            <h2 class="m-5">Корзина</h2>

            <div v-if="message" class="row d-flex justify-content-center mt-5">
                <div class="col-12 text-center" :class="message ? 'alert alert-success': ''">
                    @{{message}}
                </div>
            </div>

            <div v-if="message_danger" class="row d-flex justify-content-center mt-5">
                <div class="col-12 text-center" :class="message_danger ? 'alert alert-danger': ''">
                    @{{message_danger}}
                </div>
            </div>

            <div class="cartTable col-10">
                <div class="tabl">
                    <div class="thead row" style="font-weight: bold; border-bottom: 3px #212529 solid">
                        <div class="col-1 text-center">#</div>
                        <div class="col-5">Товар</div>
                        <div class="col-2">Стоимость</div>
                        <div class="col-2">Количество</div>
                        <div class="col-2">Действие</div>
                    </div>

                    <div class="tbody row" v-for="(cart, index) in carts">
                        <div class="col-1 d-flex align-items-center justify-content-center" style="font-weight: bold">@{{index+1}}</div>
                        <div class="col-5 d-flex align-items-center">
                            <img :src="cart.product.img" alt="product" style="width:15%; margin-right: 5%">
                                <a :href="`{{route('productPage')}}/${cart.product.id}`"><p style="font-weight: bold; margin:0;">@{{cart.product.title}}</p></a>
                        </div>
                        <div class="col-2 d-flex align-items-center" style="font-weight: bold">@{{cart.summ}} руб.</div>
                        <div class="col-2 d-flex align-items-center" style="font-weight: bold">
                            <button @click='removeFromCart(cart.product.id)' class="pmbut">-</button>
                            <p class="m-0 p-2">@{{cart.count}}</p>
                            <button @click='addToCart(cart.product.id)' class="pmbut">+</button>
                        </div>
                        <div class="col-2 d-flex align-items-center">
                            <button class="btn btn-danger" @click = 'deleteFromCart(cart.product.id)'>Удалить</button>
                        </div>
                    </div>

                    <div v-if='carts.length != 0' class="sum mt-5">
                        <p class="h4">Итого: @{{order.summ}} руб.</p>
                    </div>

                    <div v-if='carts.length == 0' class="mt-5">
                        <p class="h4 text-center">Корзина пуста</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="makeOrder mt-3 d-flex justify-content-center" v-if='carts.length != 0'>
            <!-- Button trigger modal -->
            <button type="button" class="col-3 btn btn-dark" data-bs-toggle="modal" data-bs-target="#makeOrderModal">
                Оформить заказ
            </button>

            <!-- Modal -->
            <div class="modal fade" id="makeOrderModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Оформление заказа</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent = 'makeAnOrder' id="orderForm" class="col-12" method="POST">
                                <div class="col-12 d-flex flex-wrap justify-content-center mb-3">
                                    <input type="password" style="width: 66.6666%" class="form-control" :class="errors.password ? 'is-invalid' : '' " placeholder="Введите пароль" name="password" id="password">
                                    <div style="width: 66.6666%" :class="errors.password ? 'invalid-feedback' : '' " v-for="error in errors.password">
                                        @{{error}}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary col-3" style="height: 40px">Заказать</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const Cart = {
            data(){
                return{
                    message: '',
                    message_danger:'',
                    errors: [],
                    carts: [],
                    order: '',
                }
            },

            methods:{

                async getCarts(){
                    const response = await fetch('{{route('getCarts')}}');
                    const data = await response.json();
                    this.carts = data.carts;
                    this.order = data.order;
                },

                async addToCart(id){
                    const response = await fetch('{{route('addToCart')}}', {
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                            'Content-Type': 'application/json',
                        },
                        body:JSON.stringify({
                            product_id:id,
                        })
                    });

                    if (response.status === 400){
                        this.message_danger = await response.json();
                        setTimeout(() => {
                            this.message_danger = null;
                        }, 3000);
                    }

                    this.getCarts();
                },

                async removeFromCart(id){
                    const response = await fetch('{{route('removeFromCart')}}', {
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                            'Content-Type': 'application/json',
                        },
                        body:JSON.stringify({
                            product_id:id,
                        })
                    });

                    if (response.status === 200){
                        this.message = await response.json();
                        setTimeout(() => {
                            this.message = null;
                        }, 3000);
                    }

                    this.getCarts();
                },

                async deleteFromCart(id){
                    const response = await fetch('{{route('deleteFromCart')}}', {
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                            'Content-Type': 'application/json',
                        },
                        body:JSON.stringify({
                            product_id:id,
                        })
                    });

                    if (response.status === 200){
                        this.message = await response.json();
                        setTimeout(() => {
                            this.message = null;
                        }, 3000);
                    }

                    this.getCarts();
                },

                async makeAnOrder(){
                    const form = document.querySelector('#orderForm');
                    const form_data = new FormData(form);
                    const response = await fetch('{{route('makeAnOrder')}}', {
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': '{{csrf_token()}}',
                        },
                        body:form_data,
                    });

                    if (response.status === 200){
                        document.querySelector('#makeOrderModal').classList.remove('show');
                        document.body.classList.remove('modal-open');
                        document.body.style ='';
                        document.querySelector('.modal-backdrop').remove();
                        document.querySelector('#password').value = '';
                        this.getCarts();
                        this.message = await response.json();
                        setTimeout(()=>{
                            this.message = null;
                        }, 3000)
                    }

                    if (response.status === 400){
                        this.errors = await response.json();
                    }

                    if (response.status === 403){
                        document.querySelector('#makeOrderModal').classList.remove('show');
                        document.body.classList.remove('modal-open');
                        document.body.style ='';
                        document.querySelector('.modal-backdrop').remove();
                        document.querySelector('#password').value = '';
                        this.message_danger = await response.json();
                        setTimeout(()=>{
                            this.message_danger = null;
                        }, 3000)
                    }
                }

            },

            mounted(){
                this.getCarts();
            }
        }

        Vue.createApp(Cart).mount('#cartPage');
    </script>

@endsection
