<!DOCTYPE html>
<html>
<body>
    <h2>Szia {{ $user->name }} 👋</h2>

    <p>A rendelésed a következő</p>
    @foreach ($items as $item)
    <div class="" style="display: block" >
          {{$item['name']}}
          {{$item['price']}} forint
          {{$item['description']}}
    </div>
  
    @endforeach
    <p>Teljes összeg:{{$order->total_price}} forint</p>
    <p>számlázási adatok:</p>
    {{$user->billing['billing_name']}}
    {{$user->billing['billing_zip']}}
    {{$user->billing['billing_city']}}
    {{$user->billing['billing_address_line']}}
    {{$user->billing['company_name']}}
    {{$user->billing['tax_id']}}
   
</body>
</html>