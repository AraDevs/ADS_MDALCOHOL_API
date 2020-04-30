<html>
<head>
<meta charset="UTF-8" />
    <style>
        header{
            background-color: #4c4cff;
            color:white;
            padding: 20px;
        }
        h1{
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .total{
            text-align: right;
        }
        
    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</head>
@if(isset($data))
    <header>
        <h1>MD Alcohol</h1>
        <hr>
        
        <h4>Reporte de ventas de {{ $data->first()->inventory->name }}</h4>
        <h5>{{Carbon\Carbon::now()->format("d/m/Y")}}</h5>
    </header>
    <body>
    <br>
    <br>
            <h3>Lista de ventas: </h3>
            <p class="total">Numero de productos vendidos: {{ count($data) }} </p>
            <br>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">Fecha de venta</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Precio unitario</th>
                    <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $finalTotal = 0;
                ?>
                @foreach($data as $bill)
                    <?php
                        
                        $finalTotal+=($bill->price)*($bill->quantity);
                        ?>
                    <tr>
                        <td scope="row">{{ \Carbon\Carbon::parse($bill->bill->bill_date)->format('d/m/Y') }}</td>
                        <td>{{ $bill->quantity }}</td>
                        <td>{{ $bill->price }}</td>
                        <td>${{ number_format(($bill->price)*($bill->quantity),2) }}</td>
                    </tr>
                @endforeach
                    
                </tbody>
            </table>
            <p class="total" >Total en ventas: ${{ number_format($finalTotal,2) }}</p>
    </body>
@else
    @if(isset($inventory))
        <header>
            <h1>MD Alcohol</h1>
            <h4>Reporte de ventas por {{ $inventory->name }}</h4>
            <h5>{{Carbon\Carbon::now()->format("d/m/Y")}}</h5>
        </header>
        <body>
        <br>
        <br>
                <p>El producto {{ $inventory->name }} </p>
        </body>
    @else
        <header>
            <h1>MD Alcohol</h1>
        </header>
       <p>El producto especificado no ha sido encontrado, si considera que esto es un error, por favor contacte al administrador.</p>                 
    @endif
@endif
</html>