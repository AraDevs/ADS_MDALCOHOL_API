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
        
        <h4>Reporte de ventas a {{ $data->first()->client->business_name}}</h4>
        <h5>{{Carbon\Carbon::now()->format("d/m/Y")}}</h5>
    </header>
    <body>
    <br>
    <br>
            <h3>Lista de ventas: </h3>
            <br>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                    <th scope="col">Tipo de pago</th>
                    <th scope="col">Tipo de venta</th>
                    <th scope="col">Fecha de venta</th>
                    <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $finalTotal = 0;
                ?>
                @foreach($data as $bill)
                    <?php
                        $total= 0;
                        foreach($bill->billItem as $item){
                            $total += $item->price;
                        }
                        $finalTotal+=$total;
                        ?>
                    <tr>
                        <td scope="row">{{ $bill->payment_type }}</td>
                        <td>{{ $bill->bill_type }}</td>
                        <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d/m/Y') }}</td>
                        <td>${{ number_format($total,2) }}</td>
                    </tr>
                @endforeach
                    
                </tbody>
            </table>
            <p class="total" >Ventas totales: ${{ number_format($finalTotal,2) }}</p>
    </body>
@else
    @if(isset($client))
        <header>
            <h1>MD Alcohol</h1>
            <h4>Reporte de ventas a {{ $client->business_name }}</h4>
            <h5>{{Carbon\Carbon::now()->format("d/m/Y")}}</h5>
        </header>
        <body>
        <br>
        <br>
                <p>El cliente {{ $client->business_name }} no tiene ninguna venta registrada a la fecha</p>
        </body>
    @else
        <header>
            <h1>MD Alcohol</h1>
        </header>
       <p>El cliente especificado no ha sido encontrado, si considera que esto es un error, por favor contacte al administrador.</p>                 
    @endif
@endif
</html>