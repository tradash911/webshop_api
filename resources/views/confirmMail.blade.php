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
   
</body>
</html>