<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Invoice</title>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <style type="text/css">
            * {
                font-family: Verdana, Arial, sans-serif;
            }
            table{
                font-size: x-small;
            }
            tfoot tr td{
                font-weight: bold;
                font-size: x-small;
            }
            .gray {
                background-color: lightgray
            }
        </style>
    </head>
    <body>
        <table width="100%">
            <tr>
                <td align="left">
                    <h3>Restaurant Management</h3>
                        <p><strong>Meal ID: </strong>{{ $invoice->meal_id }}</p>
                        <p><strong>Table Number: </strong>{{ $invoice->meal->table_number }}</p>
                        <p><strong>Date: </strong>{{ $invoice->date }}</p>
                        <p><strong>State: </strong>{{ $invoice->state }}</p>
                </td>
                <td align="right">
                    <h3>Client</h3>
                        <p><strong>Name: </strong>{{ $invoice->name }}</p>
                        <p><strong>NIF: </strong>{{ $invoice->nif }}</p>
                </td>
            </tr>
        </table>
        <table width="100%">
            <thead style="background-color: lightgray;">
            <tr>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Unit Price $</th>
                <th>Sub Total Price $</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($invoice->invoice_items as $invoice_item)
                <tr>
                    <th scope="row">{{ $invoice_item->item_id }}</td>
                    @foreach ($items as $item)
                        @if($item->id === $invoice_item->item_id)
                        <td>{{ $item->name }}</td>
                        @endif
                    @endforeach
                    <td align="right">{{ $invoice_item->quantity }}</td>
                    <td align="right">{{ $invoice_item->unit_price }} €</td>
                    <td align="right">{{ $invoice_item->sub_total_price }} €</td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td align="right">Total €</td>
                    <td align="right" class="gray">{{ $invoice->total_price }} €</td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>