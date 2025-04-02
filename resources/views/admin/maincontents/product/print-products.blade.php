<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print-Products</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
        }

        /* h4{
            margin-bottom: 2px;
        } */
        .main{
            width: 770px;
            margin: auto;
        }
        .label-sheet {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: repeat(13, auto);
            /* gap: 5px; */
            background: white;
        }
        .label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #ccc;
            padding: 5px;
            width: 150px;
            height: 80px;
            background: white;
            margin: 2px 3px;
            border-radius: 3px;
        }
        .label img {
            width: 38.1mm;
            height: 21.1mm;
        }
        @media print {
            @page {
                margin: 5px; /* Removes default browser margins */
            }

            /* Hide elements that are not needed in print */
            header, footer, nav, aside {
                display: none;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- <button class="print-button" onclick="window.print();" style="background: #eee; border: 1px solid #ccc; padding: 7px 13px; text-transform: uppercase; margin: auto;">Print</button> -->
    <div class="main">
        <div class="label-sheet">
            <?php if(!empty($products)){ foreach($products as $product){?>
                <div class="label">
                    <span style="font-size: 50px;"><?=$product['price']?></span>
                    <span><?=$product['name']?></span>
                </div>
            <?php } }?>
        </div>
    </div>
</body>
</html>